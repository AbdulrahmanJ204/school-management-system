<?php

namespace App\Services\Files;

use App\Enums\StringsManager\Files\FileStr;
use App\Enums\UserType;
use App\Helpers\ResponseHelper;
use App\Http\Resources\FileResource;
use App\Models\File;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use LaravelIdea\Helper\App\Models\_IH_File_QB;
use Illuminate\Support\Facades\Auth;

trait ListFiles
{
    public function list($request, $trashed = false): JsonResponse
    {
        return match (Auth::user()->user_type) {
            UserType::Admin->value =>
            $trashed ?
                $this->listTrashed($request) :
                $this->listAdminFiles($request),
            UserType::Teacher->value => $this->listTeacherFiles($request),
            UserType::Student->value => $this->listStudentFiles($request),
            default => ResponseHelper::jsonResponse([], __(FileStr::messageUnknownType->value), 403),
        };
    }

    private function listAdminFiles($request): JsonResponse
    {
        $yearId = $this->getYearId($request);
        $data = $request->validated();
        $query = File::belongsToYear($yearId)
            ->orderByPublishDate();

        $this->filterAdmin($request, $query, $data);

        $files = $query->get();
        $files->each->loadTargets();
        return ResponseHelper::jsonResponse(FileResource::collection($files), __(FileStr::messageRetrieved->value));
    }

    private function listTrashed($request): JsonResponse
    {
        $yearId = $this->getYearId($request);
        $data = $request->validated();
        $query = File::onlyTrashed()
            ->belongsToYear($yearId)
            ->orderByDeletionDate();

        $this->filterAdmin($request, $query, $data);

        $files = $query->get();
        $files->each->loadTargets();
        return ResponseHelper::jsonResponse(FileResource::collection($files), __(FileStr::messageRetrieved->value));

    }
    private function listTeacherFiles($request): JsonResponse
    {
        $data = $request->validated();
        // Teacher Can Access Only Current Assignments Files
        $teacher = Auth::user()->teacher;
        $subjectId = null;
        $sectionId = null;
//        if ($request->has('subject')) {
//            $subjectId = $data['subject'];
//        }
//        if ($request->has('section')) {
//            $sectionId = $data['section'];
//        }
        $files = File::belongsToTeacher($teacher->id, $subjectId, $sectionId)
            ->orderByPublishDate()->get();
        return ResponseHelper::jsonResponse(FileResource::collection($files), 'files retrieved successfully');
    }

    private function listStudentFiles($request): JsonResponse
    {
        $data = $request->validated();
        $yearId = $this->getYearId($request);
//        $subjectId = $request->filled('subject') ? $data['subject'] : null;

        $enrollments = Auth::user()->student->yearEnrollments($yearId);
        if ($enrollments->isEmpty()) {
            return ResponseHelper::jsonResponse([], __(FileStr::messageNoEnrollments->value), 400);

        }

        $files = collect();

        foreach ($enrollments as $enrollment) {
            $start_date = $enrollment->semester->start_date;
            $end_date = $enrollment->semester->end_date;
            $currentSemesterFiles =
                File::inDateRange($start_date, $end_date)
                    ->forSection($enrollment->section_id)
//                    ->forSubject($subjectId)
                    ->get();
            $files = $files->merge($currentSemesterFiles);
        }
        $overallStartDate = $enrollments->min('semester.start_date');
        $overallEndDate = $enrollments->max('semester.end_date');
        $gradeId = $enrollments->pluck('grade_id')->first();
        $query = File::inDateRange($overallStartDate, $overallEndDate)
            ->forGradeOrPublic($gradeId);
//            ->forSubject($subjectId);
//        $this->studentFilter($request, $query, $data);
        $gradeAndPublicFiles =
            $query->get();
        $files = $files->merge($gradeAndPublicFiles)
            ->unique('id')
            ->sortByDesc('publish_date')
            ->values();


        return ResponseHelper::jsonResponse(FileResource::collection($files), __(FileStr::messageRetrieved->value));

    }

    /**
     * @param $request
     * @param $query
     * @param mixed $data
     * @return void
     */
    public function filterAdmin($request, $query, mixed $data): void
    {

        // Subject
        if ($request->has('subject')) {
            $query->forSubject($data['subject']);
        }
        // Type
        if ($request->has('type')) {
            $query->forType($data['type']);
        }

        // Target Filtering
        if ($request->has('section')) {
            $query->forSection($data['section']);
        } else if ($request->has('grade')) {
            $query->forGrade($data['grade']);
        } else if (request()->has('general')) {
            $query->forPublic();
        }
    }

    /**
     * @param $request
     * @param $query
     * @param mixed $data
     * @return void
     */


    /**
     * @param $request
     * @param $query
     * @param $data
     * @return void
     */
    public function studentFilter($request, $query, $data): void
    {
        // Subject
        if ($request->has('subject')) {
            $query->forSubject($data['subject']);
        }
        // Type
        if ($request->has('type')) {
            $query->forType($data['type']);
        }
    }

}
