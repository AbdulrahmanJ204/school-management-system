<?php

namespace App\Services;

use App\Helpers\ResponseHelper;
use App\Http\Resources\NewsResource;
use App\Models\News;
use App\Models\NewsTarget;
use App\Models\Section;
use App\Models\Semester;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\StudentEntrollment;
use App\Models\Year;
use Illuminate\Database\Query\Builder;

class NewsService
{

    public function getNews()
    {

        $user = auth()->user();
        if ($user->role === 'student') {
            return $this->getStudentNews();
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
}
