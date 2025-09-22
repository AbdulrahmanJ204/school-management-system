<?php

namespace App\Services\Files;

use App\Enums\Permissions\FilesPermission;
use App\Enums\StringsManager\Files\FileStr;
use App\Exceptions\PermissionException;
use App\Helpers\AuthHelper;
use App\Helpers\ResponseHelper;
use App\Http\Resources\FileResource;
use App\Models\File;
use Illuminate\Http\JsonResponse;

trait RestoreFile
{
    /**
     * @throws PermissionException
     */
    public function restore($fileId): JsonResponse
    {

        AuthHelper::authorize(FilesPermission::restore->value);
        $file = File::onlyTrashed()->findOrFail($fileId);
        $file->restoreWithTargets();

        return ResponseHelper::jsonResponse(FileResource::make($file), __(FileStr::messageRestored->value));
    }

}
