<?php
namespace App\Services\Files;
use App\Enums\UserType;
use App\Helpers\ResponseHelper;
use App\Http\Resources\FileResource;
use App\Models\File;
use Illuminate\Http\JsonResponse;

trait ShowFile{

    public function show($fileId): JsonResponse
    {
        // only admin
        // maybe add AuthHelper::authorize(NewsPermission::show->value);
        $file = File::withTrashed()->findOrFail($fileId);
        return ResponseHelper::jsonResponse(FileResource::make($file), 'file retrieved successfully');

    }

}
