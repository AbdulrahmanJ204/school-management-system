<?php

namespace App\Services\Files;

use App\Enums\StringsManager\FileStr;
use App\Enums\UserType;
use App\Helpers\ResponseHelper;
use App\Http\Resources\FileResource;
use App\Models\File;
use App\Models\Year;
use Illuminate\Http\JsonResponse;

trait ListFiles
{
    public function list($request): JsonResponse
    {
        return match (auth()->user()->user_type) {
            UserType::Admin->value => $this->listAdminFiles($request),
            UserType::Teacher->value => $this->listTeacherFiles($request),
            UserType::Student->value => $this->listStudentFiles($request),
            default => ResponseHelper::jsonResponse([], __(FileStr::messageUnknownType->value), 403),
        };
    }

    private function listAdminFiles($request): JsonResponse
    {
        $yearId = $this->getYearId($request);
        $data = $request->validated();
        $query = File::withTrashed()
            ->belongsToYear($yearId)
            ->orderByPublishDate();
        if ($request->has($this->querySubject)) {
            $query->forSubject($data[$this->querySubject]);
        }
        if ($request->has($this->apiType)) {
            $query->forType($data[$this->apiType]);
        }
        $files = $query->get();
        $files->each->loadTargets();
        return ResponseHelper::jsonResponse(FileResource::collection($files), __(FileStr::messageRetrieved->value));
    }

    private function listTeacherFiles($subjectID): JsonResponse
    {
        // Teacher Can Access Only Current Assignments Files
        $teacher = auth()->user()->teacher;
        $files = File::belongsToTeacher($teacher->id, $subjectID)
            ->orderByPublishDate()
            ->get();

        return ResponseHelper::jsonResponse(FileResource::collection($files), 'files retrieved successfully');
    }

    private function listStudentFiles($request): JsonResponse
    {
        $data = $request->validated();
        $yearId = $this->getYearId($request);
        $subjectId = $request->filled($this->querySubject) ? $data[$this->querySubject] : null;

        $enrollments = auth()->user()->student->yearEnrollments($yearId);
        if ($enrollments->isEmpty()) {
            return ResponseHelper::jsonResponse([], __(FileStr::messageNoEnrollments->value), 400);

        }

        $files = collect();

        foreach ($enrollments as $enrollment) {
            $start_date = $enrollment->semester->start_date;
            $end_date = $enrollment->semester->end_date;
            $currentSemesterFiles = File::
            inDateRange($start_date, $end_date)
                ->forSection($enrollment->section_id)
                ->forSubject($subjectId)
                ->get();
            $files = $files->merge($currentSemesterFiles);
        }
        $overallStartDate = $enrollments->min('semester.start_date');
        $overallEndDate = $enrollments->max('semester.end_date');
        $gradeId = $enrollments->pluck('grade_id')->first();
        $query = File::inDateRange($overallStartDate, $overallEndDate)
            ->forGradeOrPublic($gradeId)
            ->forSubject($subjectId);
        if($request->has($this->apiType)) {
            $query->forType($data[$this->apiType]);
        }
        $gradeAndPublicFiles =
            $query->get();
        $files = $files->merge($gradeAndPublicFiles)
            ->unique('id')
            ->sortByDesc('publish_date')
            ->values();


        return ResponseHelper::jsonResponse(FileResource::collection($files), __(FileStr::messageRetrieved->value));

    }

}
