<?php

namespace App\Services\News;

use App\Enums\Permissions\NewsPermission;
use App\Enums\StringsManager\NewsStr;
use App\Exceptions\PermissionException;
use App\Helpers\AuthHelper;
use App\Helpers\ResponseHelper;
use App\Http\Resources\NewsResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

trait SoftDeleteNews
{

    /**
     * @throws PermissionException
     */
    public function softDelete($news): JsonResponse
    {

        AuthHelper::authorize(NewsPermission::softDelete->value);
        $data = $news->getDeleteSnapshot(); // can be used for debug

        $news->delete();
        return ResponseHelper::jsonResponse([],  __(NewsStr::messageSoftDelete->value));
    }


}
