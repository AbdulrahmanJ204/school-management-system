<?php

namespace App\Services;

use App\Exceptions\ImageUploadFailed;
use App\Helpers\ResponseHelper;
use App\Http\Resources\NewsResource;
use App\Models\News;
use App\Models\NewsTarget;
use App\Models\SchoolDay;
use App\Models\StudentEnrollment;
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

    public function getStudentNews(): \Illuminate\Http\JsonResponse
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
        $admin = auth()->user()->admin;
        $news = News::all();
        return ResponseHelper::jsonResponse(NewsResource::collection($news));
    }

    public function createNews($request)
    {
        $user = auth()->user();
        $data = $request->validated();

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

        // Get current school day
        $curDay = '2025-05-11'; // now()->format('Y-m-d');
        $schoolDay = SchoolDay::where('date', $curDay)->first();

        $content = $data['content'];

        // If content is a string (JSON), validate it
        if (is_string($content)) {
            $decodedContent = json_decode($content, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \InvalidArgumentException('Invalid JSON content provided');
            }

            // Validate ops structure (without delta wrapper)
            if (!isset($decodedContent['ops']) || !is_array($decodedContent['ops'])) {
                throw new \InvalidArgumentException('Content must have ops array structure');
            }
        }
        // If content is already an array, encode it
        else if (is_array($content)) {
            // Validate ops structure (without delta wrapper)
            if (!isset($content['ops']) || !is_array($content['ops'])) {
                throw new \InvalidArgumentException('Content must have ops array structure');
            }

            $content = json_encode($content);
        }

        // Create news record
        $news = News::create([
            'title' => $data['title'],
            'content' => $content, // Store as JSON string
            'photo' => $photoPath,
            'school_day_id' => $schoolDay->id,
            'created_by' => $user->id,
        ]);

        // Handle news targets
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

        return NewsResource::make($news);
    }
}
