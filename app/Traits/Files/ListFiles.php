<?php

namespace App\Traits\Files;

use App\Enums\StringsManager\FileStr;
use App\Enums\UserType;
use App\Exceptions\PermissionException;
use App\Helpers\ResponseHelper;
use App\Http\Resources\FileResource;
use App\Models\File;
use App\Models\TeacherSectionSubject;
use Illuminate\Http\JsonResponse;

trait ListFiles
{
    public function list(): JsonResponse
    {
        // TODO : Check if there is a YearId queryParameter. $request->query('year_id')
        return match (auth()->user()->user_type) {
            UserType::Admin->value => $this->getAdminFiles(),
            UserType::Teacher->value => $this->getTeacherFiles(),
            UserType::Student->value => $this->getStudentFiles(),
            default => ResponseHelper::jsonResponse([], __(FileStr::messageUnknownType->value), 403),
        };
    }

    private function getAdminFiles(): JsonResponse
    {
        $files = File::withTrashed()->orderByDesc('created_at')->get();
        $files->each(function ($file) {
            $file->loadDeletedTargets();
        });
        return ResponseHelper::jsonResponse(FileResource::collection($files), __(FileStr::messageFilesRetrieved->value));
    }

    private function getTeacherFiles(): JsonResponse
    {
        $teacher = auth()->user()->teacher;

        $files = File::withTrashed()->belongsToTeacher($teacher->id)->orderByDesc('created_at')->get();
        $files->each(function ($file) {
            $file->loadDeletedTargets();
        });
        return ResponseHelper::jsonResponse(FileResource::collection($files), 'files retrieved successfully');
    }

    private function getStudentFiles(): JsonResponse
    {
        $files = $this->getStudentFilesCollection();
        return ResponseHelper::jsonResponse(FileResource::collection($files), __(FileStr::messageFilesRetrieved->value));

    }

    private function getStudentFilesCollection()
    {
        $enrollments = auth()->user()->student->currentYearEnrollments();

        $files = collect();

        foreach ($enrollments as $enrollment) {
            $start_date = $enrollment->semester->start_date;
            $end_date = $enrollment->semester->end_date;
            $currentSemesterNews = File::where('publish_date', '>=', $start_date)
                ->where('publish_date', '<=', $end_date)
                ->whereHas('targets', function ($query) use ($enrollment) {
                    $query->where('section_id', $enrollment->section_id);
                })->get();
            $files = $files->merge($currentSemesterNews);
        }
        $overallStartDate = $enrollments->min('semester.start_date');
        $overallEndDate = $enrollments->max('semester.end_date');
        $gradeId = $enrollments->pluck('grade_id')->first();
        $gradeAndPublicNews =
            File:: where('publish_date', '>=', $overallStartDate)
                ->where('publish_date', '<=', $overallEndDate)->whereHas('targets', function ($query) use ($gradeId) {
                    $query->where('grade_id', $gradeId)->orWhere(function ($q) {
                        $q->whereNull('section_id')
                            ->whereNull('grade_id');
                    });
                })->get();
        return $files->merge($gradeAndPublicNews)
            ->unique('id')
            ->sortByDesc('publish_date')
            ->values();

    }

}
