<?php

namespace App\Traits\Files;

use App\Enums\StringsManager\FileStr;
use App\Enums\UserType;
use App\Helpers\ResponseHelper;
use App\Http\Resources\FileResource;
use App\Models\File;
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
        // TODO : Edit logic according to Alaa
        $files = File::all()->get();
        return ResponseHelper::jsonResponse(FileResource::collection($files), 'files retrieved successfully');
    }

    private function getStudentFiles(): JsonResponse
    {
        // TODO Edit this to match student news logic
        $enrollments = auth()->user()->student->currentEnrollments();
        $sectionIds = $enrollments->pluck($this->dbSectionId);
        $gradeId = $enrollments->pluck($this->dbGradeId)->unique();
        $files = File::whereHas('targets', function ($query) use ($gradeId, $sectionIds) {
            $query
                ->whereIn($this->dbSectionId, $sectionIds)
                ->orWhere($this->dbGradeId, $gradeId)
                ->orWhere(function ($q) {
                    $q->whereNull($this->dbSectionId)
                        ->whereNull($this->dbGradeId);
                }
                );
        })->orderByDesc('created_at')->get();

        $uniqueFiles = collect($files)->unique('id')->values();
        return ResponseHelper::jsonResponse(FileResource::collection($uniqueFiles), __(FileStr::messageFilesRetrieved->value));

    }

}
