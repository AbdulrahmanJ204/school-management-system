<?php

namespace App\Services;

use App\Exceptions\PermissionException;
use App\Helpers\ResponseHelper;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class AdminService
{
    /**
     * @throws PermissionException
     */
    public function listAdmins(): JsonResponse
    {
        if (!auth()->user()->hasPermissionTo('عرض المشرفين')) {
            throw new PermissionException();
        }

        $admins = User::where('user_type', 'admin')
            ->with(['admin'])
            ->orderBy('id', 'asc')
            ->paginate(15);

        return ResponseHelper::jsonResponse(
            UserResource::collection($admins),
        );
    }
}
