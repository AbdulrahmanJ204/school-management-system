<?php

namespace App\Services;

use App\Enums\NewsPermission;
use App\Enums\UserType;
use App\Exceptions\ImageUploadFailed;
use App\Exceptions\PermissionException;
use App\Helpers\AuthHelper;
use App\Helpers\ResponseHelper;
use App\Http\Resources\NewsResource;
use App\Models\News;
use App\Models\NewsTarget;
use App\Models\SchoolDay;
use App\Models\Student;
use App\Models\StudentEnrollment;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class NewsService
{

    public function listNews()
    {
        $user_type = auth()->user()->user_type;
        return match ($user_type) {
            UserType::Admin->value => $this->listAdminNews(),
            UserType::Student->value => $this->listStudentNews(),
            default => throw new PermissionException(),
        };
    }

    private function listStudentNews(): JsonResponse
    {
        $news = $this->getStudentNewsCollection();
        return ResponseHelper::jsonResponse(NewsResource::collection($news));
    }

    private function listAdminNews(): JsonResponse
    {
        $news = News::withTrashed()->get()->sortByDesc('created_at');
        $news->each(function (News $new) {
            $new->loadDeletedNewsTargets();
        });
        return ResponseHelper::jsonResponse(NewsResource::collection($news));
    }


    public function store($request): NewsResource
    {
        $user = auth()->user();
        $data = $request->validated();
        $photoPath = $this->handlePhoto($request);
        $publishDate = Carbon::now();
        $content = $this->handleContent($data['content']);
        $news = News::create([
            'title' => $data['title'],
            'content' => $content,
            'photo' => $photoPath,
            'publish_date' => $publishDate,
            'created_by' => $user->id,
        ]);

        $this->handleNewsTargetsOnCreate($request, $data, $news);
        // necessary to use load for relations here , because Autoload not work here I guess.
        $news->load('targets.grade', 'targets.section.grade');
        return NewsResource::make($news);
    }

    public function update($request, $news): JsonResponse
    {

        $data = $request->validated();
        $updateData = [];
        if ($request->hasFile('photo')) {
            $photoPath = $this->handlePhoto($request, $news->photo);
            $updateData['photo'] = $photoPath;
        }

        if ($request->filled('title')) {
            $updateData['title'] = $data['title'];
        }
        if ($request->filled('content')) {
            $content = $this->handleContent($data['content']);
            $updateData['content'] = $content;
        }

        $this->updateNewsTargetsOnUpdate($request, $data, $news);
        $news->update($updateData);
        $news->load('targets.section.grade', 'targets.grade');
        return ResponseHelper::jsonResponse(NewsResource::make($news), 'news updated');
    }

    public function show($newsId): JsonResponse
    {

        $user_type = auth()->user()->user_type;
        return match ($user_type) {
            UserType::Admin->value => $this->showAdminNews($newsId),
            UserType::Student->value => $this->showStudentNews($newsId),
            default => throw new PermissionException(),
        };
    }

    /**
     * @throws PermissionException
     */
    public function destroy($news): JsonResponse
    {

        AuthHelper::authorize(NewsPermission::softDelete->value);

        $data = clone $news;
        $data->load('targets.section.grade', 'targets.grade');
        DB::transaction(function () use ($news) {
            $news->targets()->delete();
            $news->delete();
        });

        return ResponseHelper::jsonResponse(NewsResource::make($data), 'news deleted');
    }

    /**
     * @throws PermissionException
     */
    public function restore($newsId)
    {
        AuthHelper::authorize(NewsPermission::restore->value);
        $news = News::onlyTrashed()->findOrFail($newsId);
        $deleteDate = $news->deleted_at;
        $news->restore();
        $targets = NewsTarget::onlyTrashed()
            ->where('news_id', $newsId)
            ->where('deleted_at', $deleteDate)
            ->with(['section.grade', 'grade'])
            ->get();
        $targets->each->restore();
        $news->setRelation('targets', $targets);
        return ResponseHelper::jsonResponse(NewsResource::make($news), 'news restored successfully');

    }

    private function handlePhoto($request, $deletePath = null): ?string
    {
        $photoPath = null;
        if ($request->hasFile('photo')) {
            try {
                if ($deletePath && Storage::disk('public')->exists($deletePath)) {
                    Storage::disk('public')->delete($deletePath);
                }
                $image = $request->file('photo');
                $imageName = $image->hashName();
                $imagePath = 'news_images/' . $imageName;

                if (!Storage::disk('public')->exists($imagePath)) {
                    $image->storeAs('news_images', $imageName, 'public');
                }
                $photoPath = $imagePath;

            } catch (\Exception $e) {
                throw new ImageUploadFailed();
            }
        }
        return $photoPath;
    }


    private function handleContent(mixed $content): mixed
    {
        if (is_string($content)) {
            $decodedContent = json_decode($content, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \InvalidArgumentException('Invalid JSON content provided');
            }

            if (!isset($decodedContent['ops']) || !is_array($decodedContent['ops'])) {
                throw new \InvalidArgumentException('Content must have ops array structure');
            }
        }
        return $content;
    }

    private function handleNewsTargetsOnCreate($request, $data, $news)
    {
        $user = auth()->user();
        $targets = [];
        if ($request->filled('section_ids')) {
            foreach ($data['section_ids'] as $section_id) {
                $targets[] = NewsTarget::create([
                    'news_id' => $news->id,
                    'grade_id' => null,
                    'section_id' => $section_id,
                    'created_by' => $user->id,
                ]);
            }
        } else if ($request->filled('grade_ids')) {
            foreach ($data['grade_ids'] as $grade_id) {
                $targets[] = NewsTarget::create([
                    'news_id' => $news->id,
                    'grade_id' => $grade_id,
                    'section_id' => null,
                    'created_by' => $user->id,
                ]);
            }
        } else {
            NewsTarget::create([
                'news_id' => $news->id,
                'grade_id' => null,
                'section_id' => null,
                'created_by' => $user->id,
            ]);
        }
        return $targets;
    }

    private function updateSections($news, $data): void
    {
        $user = auth()->user();
        $news->targets()->whereNotNull('grade_id')->delete();
        $news->targets()->whereNull('section_id')->whereNull('grade_id')->delete();

        $existingSections = $news->targets()
            ->whereNotNull('section_id')
            ->whereNull('grade_id')
            ->pluck('section_id')
            ->toArray();


        $sectionsToDelete = array_diff($existingSections, $data['section_ids']);
        $sectionsToAdd = array_diff($data['section_ids'], $existingSections);
        $news->targets()
            ->whereIn('section_id', $sectionsToDelete)
            ->whereNull('grade_id')
            ->delete();
        foreach ($sectionsToAdd as $section_id) {
            NewsTarget::create([
                'news_id' => $news->id,
                'grade_id' => null,
                'section_id' => $section_id,
                'created_by' => $user->id,
            ]);
        }
    }


    private function updateGrades($news, $data): void
    {
        $user = auth()->user();
        $news->targets()->whereNotNull('section_id')->delete();
        $news->targets()->whereNull('section_id')->whereNull('grade_id')->delete();
        $existingGrades = $news->targets()
            ->whereNull('section_id')
            ->whereNotNull('grade_id')
            ->pluck('grade_id')
            ->toArray();

        $gradesToDelete = array_diff($existingGrades, $data['grade_ids']);
        $gradesToAdd = array_diff($data['grade_ids'], $existingGrades);
        $news->targets()
            ->whereIn('grade_id', $gradesToDelete)
            ->whereNull('section_id')
            ->delete();

        foreach ($gradesToAdd as $grade_id) {
            NewsTarget::create([
                'news_id' => $news->id,
                'grade_id' => $grade_id,
                'section_id' => null,
                'created_by' => $user->id,
            ]);
        }
    }


    private function updateNewsTargetsOnUpdate($request, $data, $news): void
    {
        $user = auth()->user();
        if ($request->filled('section_ids')) {
            $this->updateSections($news, $data);
        } else if ($request->filled('grade_ids')) {
            $this->updateGrades($news, $data);
        } else if ($request->filled('is_global') && $data['is_global']) {
            $news->targets()->delete();
            NewsTarget::create([
                'news_id' => $news->id,
                'grade_id' => null,
                'section_id' => null,
                'created_by' => $user->id,
            ]);
        }
    }

    /**
     * @param $newsId
     * @return JsonResponse
     */
    public function showAdminNews($newsId): JsonResponse
    {
        $news = News::withTrashed()->findOrFail($newsId);
        $news->loadDeletedNewsTargets();
        return ResponseHelper::jsonResponse(NewsResource::make($news));
    }

    /**
     * @param $newsId
     * @return JsonResponse
     */
    public function showStudentNews($newsId): JsonResponse
    {
        $news = News::findOrFail($newsId);
        $studentNews = $this->getStudentNewsCollection();
        if ($studentNews->contains('id', $newsId)) {
            return ResponseHelper::jsonResponse(NewsResource::make($news));
        }
        return ResponseHelper::jsonResponse([], 'unauthorized', 403, false);
    }

    /**
     * @return mixed
     */
    private function getStudentNewsCollection(): mixed
    {
        $enrollments = auth()->user()->student->currentYearEnrollments();
        $news = collect();
        foreach ($enrollments as $enrollment) {
            $start_date = $enrollment->semester->start_date;
            $end_date = $enrollment->semester->end_date;

            $currentSemesterNews = News::where('publish_date', '>=', $start_date)
                ->where('publish_date', '<=', $end_date)
                ->whereHas('targets', function ($query) use ($enrollment) {
                    $query->where('section_id', $enrollment->section_id);
                })->get();
            $news = $news->merge($currentSemesterNews);
        }
        $overallStartDate = $enrollments->min('semester.start_date');
        $overallEndDate = $enrollments->max('semester.end_date');
        $gradeId = $enrollments->pluck('grade_id')->first();
        $gradeAndPublicNews =
            News:: where('publish_date', '>=', $overallStartDate)
                ->where('publish_date', '<=', $overallEndDate)->whereHas('targets', function ($query) use ($gradeId) {
                    $query->where('grade_id', $gradeId)->orWhere(function ($q) {
                        $q->whereNull('section_id')
                            ->whereNull('grade_id');
                    });
                })->get();
        return $news->merge($gradeAndPublicNews)
            ->unique('id')
            ->sortByDesc('created_at')
            ->values();
    }

    /**
     * @throws PermissionException
     */
    public function delete($newsId): JsonResponse
    {
        AuthHelper::authorize(NewsPermission::delete->value);
       $news = News::onlyTrashed()->findOrFail($newsId);
        $clone = clone  $news;
        $clone->load('targets.section.grade', 'targets.grade');
        DB::transaction(function () use ($news) {
            $news->targets()->withTrashed()->forceDelete();
            $news->forceDelete();
        });
        return ResponseHelper::jsonResponse(NewsResource::make($clone) , 'News Deleted Permanently');
    }
}
