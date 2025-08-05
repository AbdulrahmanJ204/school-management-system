<?php

namespace App\Services\News;

use App\Enums\StringsManager\NewsStr;
use App\Enums\UserType;
use App\Exceptions\PermissionException;
use App\Helpers\ResponseHelper;
use App\Http\Resources\NewsResource;
use App\Models\News;
use Illuminate\Http\JsonResponse;

trait ListNews
{
    public function list($request): JsonResponse
    {
        $yearId = $this->getYearId($request);
        $user_type = auth()->user()->user_type;
        return match ($user_type) {
            UserType::Admin->value => $this->listAdminNews($yearId),
            UserType::Student->value => $this->listStudentNews($yearId),
            default => throw new PermissionException(),
        };
    }

    private function listStudentNews($yearId): JsonResponse
    {
        $enrollments = auth()->user()->student->yearEnrollments($yearId);
        if ($enrollments->isEmpty()) {
            return ResponseHelper::jsonResponse([], __(NewsStr::messageNoEnrollments->value), 400);
        }
        $news = collect();
        foreach ($enrollments as $enrollment) {
            $start_date = $enrollment->semester->start_date;
            $end_date = $enrollment->semester->end_date;
            $currentSemesterNews =
                News::inDateRange($start_date, $end_date)
                    ->forSection($enrollment->section_id)
                    ->get();
            $news = $news->merge($currentSemesterNews);
        }
        $overallStartDate = $enrollments->min('semester.start_date');
        $overallEndDate = $enrollments->max('semester.end_date');
        $gradeId = $enrollments->pluck('grade_id')->first();
        $gradeAndPublicNews =
            News::inDateRange($overallStartDate, $overallEndDate)
                ->forGradeOrPublic($gradeId)
                ->get();
        $news = $news->merge($gradeAndPublicNews)
            ->unique('id')
            ->sortByDesc('publish_date')
            ->values();
        return ResponseHelper::jsonResponse(NewsResource::collection($news), __(NewsStr::messageRetrieved->value));
    }

    private function listAdminNews($yearId): JsonResponse
    {
        $news = News::withTrashed()
            ->belongsToYear($yearId)
            ->orderByPublishDate()
            ->get();

        return ResponseHelper::jsonResponse(NewsResource::collection($news), __(NewsStr::messageRetrieved->value));
    }


}
