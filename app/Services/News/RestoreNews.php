<?php

namespace App\Services\News;

use App\Enums\Permissions\NewsPermission;
use App\Enums\StringsManager\NewsStr;
use App\Exceptions\PermissionException;
use App\Helpers\AuthHelper;
use App\Helpers\ResponseHelper;
use App\Http\Resources\NewsResource;
use App\Models\News;
use App\Models\NewsTarget;
use Illuminate\Http\JsonResponse;

trait RestoreNews
{
    /**
     * @throws PermissionException
     */
    public function restore($newsId): JsonResponse
    {
        AuthHelper::authorize(NewsPermission::restore->value);
        $news = News::onlyTrashed()->findOrFail($newsId);
        $news->restoreWithTargets();
        return ResponseHelper::jsonResponse(NewsResource::make($news),  __(NewsStr::messageRestored->value));

    }


}
