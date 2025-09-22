<?php

namespace App\Services\News;

use App\Enums\Permissions\NewsPermission;
use App\Enums\StringsManager\NewsStr;
use App\Exceptions\PermissionException;
use App\Helpers\AuthHelper;
use App\Helpers\ResponseHelper;
use App\Http\Resources\NewsResource;
use App\Models\News;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

trait ForceDeleteNews
{
    /**
     * @throws PermissionException
     */
    public function delete($newsId): JsonResponse
    {
        AuthHelper::authorize(NewsPermission::delete->value);
        $news = News::onlyTrashed()->findOrFail($newsId);
        $clone = $news->getDeleteSnapshot();
        $news->forceDelete();
        return ResponseHelper::jsonResponse(NewsResource::make($clone), __(NewsStr::messageForceDelete->value));
    }

}
