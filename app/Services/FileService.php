<?php

namespace App\Services;

use App\Enums\Permissions\FilesPermission;
use App\Enums\StringsManager\FileStr;
use App\Exceptions\PermissionException;
use App\Helpers\AuthHelper;
use App\Helpers\ResponseHelper;
use App\Http\Resources\FileResource;
use App\Models\File;
use App\Traits\Files\FileHelpers;
use App\Traits\Files\InitFiles;
use App\Traits\Files\ListFiles;
use App\Traits\Files\ShowFile;
use App\Traits\Files\SoftDeleteFile;
use App\Traits\Files\StoreFile;
use App\Traits\Files\UpdateFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

// NOTES : FOR ADMIN , Files are done?


class FileService
{
    use InitFiles, FileHelpers,
        ListFiles, ShowFile,
        StoreFile, UpdateFile,
        SoftDeleteFile;

    // API keys
    private string $apiTitle;
    private string $apiDescription;
    private string $apiSubjectId;
    private string $apiIsGeneral;
    private string $apiFile;
    private string $apiType;
    private string $apiNoSubject;
    private string $apiGradeIds;
    private string $apiSectionIds;
    // General Variables
    private string $storageDisk;
    private string $generalPath;


    public function __construct()
    {
        $this->apiKeys();
        $this->generalVariables();
    }

    // --------------------------Restore--------------------------

    /**
     * @throws PermissionException
     */
    public function restore($fileId): JsonResponse
    {

        AuthHelper::authorize(FilesPermission::restore->value);
        $file = File::onlyTrashed()->findOrFail($fileId);
        $file->loadDeletedTargets();

        $file->restore();
        $file->targets->each->restore();

        return ResponseHelper::jsonResponse(FileResource::make($file), __(FileStr::messageRestored->value));
    }


    // --------------------------Download--------------------------
    public function download($fileId)
    {
        // TODO : implement this

    }

    // --------------------------Force Delete--------------------------

    /**
     * @throws PermissionException
     * @throws Throwable
     */
    public function delete($fileId): JsonResponse
    {
        AuthHelper::authorize(FilesPermission::delete->value);

        $file = File::onlyTrashed()->findOrFail($fileId);
        $this->deleteFileFromStorage($file->file);

        $clone = clone $file;
        $clone->loadDeletedTargets();

        DB::transaction(function () use ($file) {
            $file->targets()->withTrashed()->forceDelete();
            $file->forceDelete();
        });


        return ResponseHelper::jsonResponse(FileResource::make($clone), __(FileStr::messageDeletePermanent->value));
    }



}
