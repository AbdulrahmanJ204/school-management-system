<?php

namespace App\Services;

use App\Exceptions\PermissionException;
use App\Helpers\ResponseHelper;
use App\Http\Resources\PermissionResource;
use Spatie\Permission\Models\Permission;

class PermissionService
{
    public function listPermissions()
    {
        $user = auth()->user();

        if(!$user->hasPermissionTo('عرض الصلاحيات')) {
            throw new PermissionException();
        }

        return ResponseHelper::jsonResponse(
            PermissionResource::collection(Permission::all()),
            __('messages.permission.list'),
            201
        );
    }
}
