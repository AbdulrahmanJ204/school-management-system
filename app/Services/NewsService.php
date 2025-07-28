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
use App\Models\StudentEnrollment;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
        $student = auth()->user()->student;
        $enrollments = StudentEnrollment::where('student_id', $student->id)
            ->whereHas('semester.year', function ($query) {
                $query->where('is_active', true);
            })
            ->get();
        $sectionIds = $enrollments->pluck('section_id');
        $gradeId = $enrollments->pluck('grade_id')->unique();
        $news = News::whereHas('newsTargets', function ($query) use ($gradeId, $sectionIds) {
            $query
                ->whereIn('section_id', $sectionIds)
                ->orWhere('grade_id', $gradeId)
                ->orWhere(function ($q) {
                    $q->whereNull('section_id')
                        ->whereNull('grade_id');
                }
                );
        })->orderBy('created_at', 'desc')->get();

        $uniqueNews = collect($news)->unique('id')->values();
        return ResponseHelper::jsonResponse(NewsResource::collection($uniqueNews));
    }

    private function listAdminNews(): JsonResponse
    {
        $news = News::withTrashed()->get();
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
        $schoolDayID = $this->getLastSchoolDayID();
        $content = $this->handleContent($data['content']);
        $news = News::create([
            'title' => $data['title'],
            'content' => $content,
            'photo' => $photoPath,
            'school_day_id' => $schoolDayID,
            'created_by' => $user->id,
        ]);

         $this->handleNewsTargetsOnCreate($request, $data, $news);
        // necessary to use load for relations here here , because auto load not work here i guess.
        $news->load('newsTargets.grade' , 'newsTargets.section.grade');
        return NewsResource::make($news);
    }

    public function update($request, $news)
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
        $news->load('newsTargets.section.grade', 'newsTargets.grade');
        return ResponseHelper::jsonResponse(NewsResource::make($news), 'news updated');
    }

    public function show($newsId): JsonResponse
    {
        //AuthHelper::authorize(NewsPermission::show->value);
        $user_type = auth()->user()->user_type;

        $news = null;
        if ($user_type === UserType::Admin->value) {
            $news = News::withTrashed()->findOrFail($newsId);
            $targets = NewsTarget::withTrashed()
                ->where('news_id', $newsId)
                ->where('deleted_at', $news->deleted_at)
                ->with(['section.grade', 'grade'])->get();
            $news->setRelation('newsTargets', $targets);
        }
        if ($user_type === UserType::Student->value) {
            $news = News::findOrFail($newsId)
                ->with('newsTargets.section.grade', 'newsTargets.grade')
                ->first();
        }
        return ResponseHelper::jsonResponse(NewsResource::make($news));
    }

    /**
     * @throws PermissionException
     */
    public function delete($news)
    {

        AuthHelper::authorize(NewsPermission::delete->value);

        $data = clone $news;
        $data->load('newsTargets.section.grade', 'newsTargets.grade');
        DB::transaction(function () use ($news) {
            $news->newsTargets()->delete();
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
        $news = News::onlyTrashed()->findorFail($newsId);
        $deleteDate = $news->deleted_at;
        $news->restore();
        $targets = NewsTarget::onlyTrashed()
            ->where('news_id', $newsId)
            ->where('deleted_at', $deleteDate)
            ->with(['section.grade', 'grade'])
            ->get();
        $targets->each->restore();
        $news->setRelation('newsTargets', $targets);
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

    private function getLastSchoolDayID()
    {
        $today = now()->toDateString();

        $todaySchoolDay = SchoolDay::where('date', $today)->first();

        if ($todaySchoolDay) {
            return $todaySchoolDay->id;
        }

        $lastSchoolDay = SchoolDay::where('date', '<', $today)
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        return $lastSchoolDay ? $lastSchoolDay->id : null;
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
        $news->newsTargets()->whereNotNull('grade_id')->delete();
        $news->newsTargets()->whereNull('section_id')->whereNull('grade_id')->delete();

        $existingSections = $news->newsTargets()
            ->whereNotNull('section_id')
            ->whereNull('grade_id')
            ->pluck('section_id')
            ->toArray();


        $sectionsToDelete = array_diff($existingSections, $data['section_ids']);
        $sectionsToAdd = array_diff($data['section_ids'], $existingSections);
        $news->newsTargets()
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
        $news->newsTargets()->whereNotNull('section_id')->delete();
        $news->newsTargets()->whereNull('section_id')->whereNull('grade_id')->delete();
        $existingGrades = $news->newsTargets()
            ->whereNull('section_id')
            ->whereNotNull('grade_id')
            ->pluck('grade_id')
            ->toArray();

        $gradesToDelete = array_diff($existingGrades, $data['grade_ids']);
        $gradesToAdd = array_diff($data['grade_ids'], $existingGrades);
        $news->newsTargets()
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
            $news->newsTargets()->delete();
            NewsTarget::create([
                'news_id' => $news->id,
                'grade_id' => null,
                'section_id' => null,
                'created_by' => $user->id,
            ]);
        }
    }


}
