<?php
namespace App\Traits\Files;

use App\Enums\Permissions\FilesPermission;
use App\Enums\StringsManager\FileStr;
use App\Enums\UserType;
use App\Exceptions\PermissionException;
use App\Helpers\AuthHelper;
use App\Helpers\ResponseHelper;
use App\Http\Resources\FileResource;
use App\Models\File;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

trait SoftDeleteFile{

    /**
     * @throws Throwable
     * @throws PermissionException
     */
    public function destroy(File $file): JsonResponse
    {

        AuthHelper::authorize(FilesPermission::softDelete->value);

        $userType = auth()->user()->user_type;

        return match ($userType) {
            UserType::Admin->value => $this->adminSoftDelete($file),
            //TODO Add Teacher and authorize it
            default => ResponseHelper::jsonResponse([], __(FileStr::messageUnknownType->value), 400),
        };

    }

    /**
     * @param File $file
     * @return JsonResponse
     * @throws PermissionException
     * @throws Throwable
     */
    public function adminSoftDelete(File $file): JsonResponse
    {
        $data = clone $file;

        DB::
        transaction(function () use ($file) {
            $file->targets()->delete();
            $file->delete();
        });

        return ResponseHelper::jsonResponse(FileResource::make($data), __(FileStr::messageDeleted->value));
    }
}
