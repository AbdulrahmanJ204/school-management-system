<?php

namespace App\Services;

use App\Exceptions\PermissionException;
use App\Helpers\ResponseHelper;
use App\Http\Resources\UserResource;
use App\Models\User;

class AdminService
{
    public function listAdmins()
    {
        if (!auth()->user()->hasPermissionTo('list_admins')) {
            throw new PermissionException();
        }

        $admins = User::select('id', 'first_name', 'father_name', 'last_name', 'gender', 'birth_date', 'email', 'phone', 'role', 'image')
            ->where('role', 'admin')
            ->with(['admin'])
            ->orderBy('id', 'asc')
            ->paginate(15);

        return ResponseHelper::jsonResponse(
            UserResource::collection($admins),
        );
    }
}
