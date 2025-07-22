<?php

namespace App\Services;

use App\Exceptions\ImageUploadFailed;
use App\Helpers\ResponseHelper;
use App\Http\Resources\NewsResource;
use App\Models\News;
use App\Models\NewsTarget;
use App\Models\SchoolDay;
use App\Models\StudentEnrollment;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class NewsService
{

    public function getNews()
    {
        $user = auth()->user();
        if ($user->role === 'student') {
            return $this->getStudentNews();
        }
        // Teacher has no news .
        if ($user->role === 'admin') {
            return $this->getAdminNews();
        }

    }

    public function getStudentNews(): JsonResponse
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

    public function getAdminNews()
    {
        $news = News::with('newsTargets.section.grade' , 'newsTargets.grade')->get();
        return ResponseHelper::jsonResponse(NewsResource::collection($news));
    }

    public function handlePhoto($request)
    {
        $photoPath = null;
        if ($request->hasFile('photo')) {
            try {
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
    public function getLastSchoolDayID(){
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
    public function createNews($request): NewsResource
    {
        $user = auth()->user();
        $data = $request->validated();
        $photoPath = $this->handlePhoto($request);


        $schoolDayID = $this->getLastSchoolDayID();

        $content = $data['content'];
        $content = $this->handleContent($content);

        // Create news record
        $news = News::create([
            'title' => $data['title'],
            'content' => $content, // Store as JSON string
            'photo' => $photoPath,
            'school_day_id' => $schoolDayID,
            'created_by' => $user->id,
        ]);

        // Handle news targets
        $this->handleNewsTargetsOnCreate($request, $news);

        return NewsResource::make($news);
    }

    public function updateNews($request, $news)
    {

        $user = auth()->user();
        $data = $request->validated();

        $photoPath = $this->handlePhoto($request);


        $content = $this->handleContent($data['content']);
        $data['content'] = $content;

        if ($request->filled('section_ids')) {

            $this->updateSections($news, $request);

        } else if ($request->filled('grade_ids')) {

            $this->updateGrades($news, $request);

        } else {
            NewsTarget::where('news_id', $news->id)->delete();
            NewsTarget::create([
                'news_id' => $news->id,
                'grade_id' => null,
                'section_id' => null,
                'created_by' => $user->id,
            ]);
        }
        if ($photoPath) {
            $data['photo'] = $photoPath;
        }
        $news->update($data);
        return ResponseHelper::jsonResponse(NewsResource::make($news), 'news updated');
    }

    public function showNews(News $news)
    {
        return ResponseHelper::jsonResponse(NewsResource::make($news));
    }

    public function deleteNews($news)
    {
        $data = clone $news;

        NewsTarget::where('news_id', $news->id)->delete();
        $news->delete();

        return ResponseHelper::jsonResponse(NewsResource::make($data), 'news deleted');
    }

    public function handleContent(mixed $content): mixed
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

    public function handleNewsTargetsOnCreate($request, $news): void
    {
        $user = auth()->user();
        if ($request->filled('section_ids')) {
            foreach ($request->section_ids as $section_id) {
                NewsTarget::create([
                    'news_id' => $news->id,
                    'grade_id' => null,
                    'section_id' => $section_id,
                    'created_by' => $user->id,
                ]);
            }
        } else if ($request->filled('grade_ids')) {
            foreach ($request->grade_ids as $grade_id) {
                NewsTarget::create([
                    'news_id' => $news->id,
                    'grade_id' => $grade_id,
                    'section_id' => null,
                    'created_by' => $user->id,
                ]);
            }
        } else {
            // Target all users
            NewsTarget::create([
                'news_id' => $news->id,
                'grade_id' => null,
                'section_id' => null,
                'created_by' => $user->id,
            ]);
        }
    }

    public function updateSections($news, $request): void
    {
        $user = auth()->user();
        NewsTarget::where('news_id', $news->id)->whereNotNull('grade_id')->delete();
        NewsTarget::where('news_id', $news->id)->whereNull('section_id')->whereNull('grade_id')->delete();

        $existingSections = NewsTarget::where('news_id', $news->id)
            ->whereNotNull('section_id')
            ->whereNull('grade_id')
            ->pluck('section_id')
            ->toArray();


        $sectionsToDelete = array_diff($existingSections, $request->section_ids);
        $sectionsToAdd = array_diff($request->section_ids, $existingSections);
        NewsTarget::where('news_id', $news->id)
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


    public function updateGrades($news, $request): void
    {
        $user = auth()->user();
        NewsTarget::where('news_id', $news->id)->whereNotNull('section_id')->delete();
        NewsTarget::where('news_id', $news->id)->whereNull('section_id')->whereNull('grade_id')->delete();
        $existingGrades = NewsTarget::where('news_id', $news->id)
            ->whereNull('section_id')
            ->whereNotNull('grade_id')
            ->pluck('grade_id')
            ->toArray();

        $gradesToDelete = array_diff($existingGrades, $request->grade_ids);
        $gradesToAdd = array_diff($request->grade_ids, $existingGrades);
        NewsTarget::where('news_id', $news->id)
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

}
