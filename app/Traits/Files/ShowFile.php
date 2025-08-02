<?php
namespace App\Traits\Files;
use App\Enums\UserType;
use App\Helpers\ResponseHelper;
use App\Http\Resources\FileResource;
use App\Models\File;
use Illuminate\Http\JsonResponse;

trait ShowFile{

    public function show($fileId): JsonResponse
    {
        $userType = auth()->user()->user_type;

        return match ($userType) {
            UserType::Student->value => $this->showStudentFile($fileId),
            UserType::Admin->value => $this->showFileAdmin($fileId),
            UserType::Teacher->value => $this->showTeacherFile($fileId),
        };

    }

    private function showFileAdmin($id): JsonResponse
    {
        $file = File::withTrashed()->findOrFail($id);
        $file->loadDeletedTargets();
        return ResponseHelper::jsonResponse(FileResource::make($file), 'file retrieved successfully');
    }

    private function showStudentFile($fileId): JsonResponse
    {
        $file = File::findOrFail($fileId);

        // TODO : here should be as student news logic ,
        return ResponseHelper::jsonResponse(FileResource::make($file), 'file retrieved successfully');

    }

    private function showTeacherFile($fileId): JsonResponse
    {
        $file = File::findOrFail($fileId);
        // I don't know if there should be any authorization here.
        return ResponseHelper::jsonResponse(FileResource::make($file), 'file retrieved successfully');

    }

}
