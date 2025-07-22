<?php

namespace App\Services;

use App\Exceptions\PermissionException;
use App\Helpers\ResponseHelper;
use App\Http\Resources\RoleResource;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class RoleService
{
    public function create($request)
    {
        $user = auth()->user();

        if($user->hasPermissionTo('انشاء دور')) {
            throw new PermissionException();
        }

        $credentials = $request->validated();

        try {
            DB::beginTransaction();

            $role = Role::create([
                'name' => $credentials['name'],
                'guard_name' => 'api',
            ]);

            if (!empty($credentials['permissions'])) {
                $role->syncPermissions($credentials['permissions']);
            }

            DB::commit();

            return ResponseHelper::jsonResponse(
                null,
                __('messages.role.created'),
                201
            );
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }
}
