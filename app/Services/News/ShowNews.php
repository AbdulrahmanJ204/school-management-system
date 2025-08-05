<?php

namespace App\Services\News;

use App\Enums\StringsManager\NewsStr;
use App\Helpers\ResponseHelper;
use App\Http\Resources\NewsResource;
use App\Models\News;
use Illuminate\Http\JsonResponse;

trait ShowNews
{
    public function show($newsId): JsonResponse
    {
        // only admin
        // maybe add AuthHelper::authorize(NewsPermission::show->value);
        $news = News::withTrashed()->findOrFail($newsId);
        return ResponseHelper::jsonResponse(NewsResource::make($news) ,  __(NewsStr::messageShow->value));
    }
}
