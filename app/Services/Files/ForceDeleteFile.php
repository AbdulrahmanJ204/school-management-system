<?php

namespace App\Services\Files;

use App\Enums\Permissions\FilesPermission;
use App\Enums\StringsManager\FileStr;
use App\Exceptions\PermissionException;
use App\Helpers\AuthHelper;
use App\Helpers\ResponseHelper;
use App\Http\Resources\FileResource;
use App\Models\File;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

trait ForceDeleteFile
{
    /**
     * @throws PermissionException
     *
     */
    public function delete($fileId): JsonResponse
    {
        AuthHelper::authorize(FilesPermission::delete->value);

        $file = File::onlyTrashed()->findOrFail($fileId);
        $clone = $file->getDeleteSnapshot();
        $file->forceDelete();


        return ResponseHelper::jsonResponse(FileResource::make($clone), __(FileStr::messageForceDelete->value));
    }


}
