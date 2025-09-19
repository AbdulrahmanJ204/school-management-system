<?php

namespace App\Services\News;

use App\Enums\StringsManager\NewsStr;
use App\Enums\UserType;
use App\Exceptions\PermissionException;
use App\Helpers\ResponseHelper;
use App\Http\Requests\ListDeletedNewsRequest;
use App\Http\Resources\NewsResource;
use App\Models\News;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

trait ListNews
{

    public function list($request, $trashed = false): JsonResponse
    {
        $yearId = $this->getYearId($request);
        $user_type = Auth::user()->user_type;
        return match ($user_type) {
            UserType::Admin->value => $trashed ?
                $this->listDeleted($request) :
                $this->listAdminNews($yearId, $request),

            UserType::Student->value => $this->listStudentNews($yearId),
            default => throw new PermissionException(),
        };
    }

    private function listStudentNews($yearId): JsonResponse
    {
        $enrollments = Auth::user()->student->yearEnrollments($yearId);
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

    private function listAdminNews($yearId, $request, $trashed = false): JsonResponse
    {
        $data = $request->validated();
        $query = News::belongsToYear($yearId)
            ->orderByPublishDate();
        $this->filterAdmin($request, $query, $data);
        $news = $query->get();
        return ResponseHelper::jsonResponse(NewsResource::collection($news), __(NewsStr::messageRetrieved->value));
    }

    /**
     * @param $request
     * @param $query
     * @param mixed $data
     * @return void
     */
    public function filterAdmin($request, $query, mixed $data): void
    {
        if ($request->has('section')) {
            $query->forSection($data['section']);
        } else if ($request->has('grade')) {
            $query->forGrade($data['grade']);
        } else if ($request->has('general')) {
            $query->forPublic();
        }
    }

    public function listDeleted(ListDeletedNewsRequest $request): JsonResponse
    {
        $data = $request->validated();
        $yearId = $this->getYearId($request);
        $query = News::onlyTrashed()
            ->belongsToYear($yearId)
            ->orderByDeletionDate();
        $this->filterAdmin($request, $query, $data);
        $news = $query->get();
        $news->each->loadTargets(); // Necessary because it is deleted
        return ResponseHelper::jsonResponse(NewsResource::collection($news), __(NewsStr::messageRetrieved->value));


    }
}
