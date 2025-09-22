<?php

namespace App\Services\Files;

use App\Enums\Permissions\FilesPermission;
use App\Enums\StringsManager\Files\FileStr;
use App\Enums\UserType;
use App\Exceptions\PermissionException;
use App\Helpers\AuthHelper;
use App\Helpers\ResponseHelper;
use App\Http\Resources\FileResource;
use App\Models\File;
use Illuminate\Http\JsonResponse;
use Throwable;
use Illuminate\Support\Facades\Auth;

trait SoftDeleteFile
{

    /**
     * @throws Throwable
     * @throws PermissionException
     */
    public function softDelete(File $file): JsonResponse
    {

        AuthHelper::authorize(FilesPermission::softDelete->value);

        $userType = Auth::user()->user_type;

        return match ($userType) {
            UserType::Admin->value => $this->adminSoftDelete($file),
            UserType::Teacher->value => $this->teacherSoftDelete($file),
            default => ResponseHelper::jsonResponse([], __(FileStr::messageUnknownType->value), 400),
        };

    }

    /**
     * @param File $file
     * @return JsonResponse
     */
    public function adminSoftDelete(File $file): JsonResponse
    {
        $data = $file->getDeleteSnapshot();
        $file->delete();

        return ResponseHelper::jsonResponse(FileResource::make($data), __(FileStr::messageSoftDelete->value));
    }

    /**
     * @throws PermissionException
     */
    private function teacherSoftDelete(File $file): JsonResponse
    {
        // Note : file would be for teacher only

        $fileBelongsToOneTeacher = $file->belongsToOneTeacher();
        if (!$fileBelongsToOneTeacher) {
            throw new PermissionException();
        }
        $data = $file->getDeleteSnapshot();
        $file->delete();


        return ResponseHelper::jsonResponse(FileResource::make($data), __(FileStr::messageSoftDelete->value));

    }

}
