<?php

namespace App\Services;

use App\Exceptions\PermissionException;
use App\Exceptions\RoleNotFoundException;
use App\Helpers\ResponseHelper;
use App\Http\Resources\RoleResource;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class RoleService
{
    public function create($request)
    {
        $user = auth()->user();

        if(!$user->hasPermissionTo('انشاء دور')) {
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
                new RoleResource($role),
                __('messages.role.created'),
                201
            );
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }

    public function update($request, $id) {

        $admin = auth()->user();

        if (!$admin->hasPermissionTo('تعديل دور')) {
            throw new PermissionException();
        }

        $credentials = $request->validated();

        $role = Role::find($id);

        $role->update([
            'name' => $credentials['name'],
        ]);

        $role->syncPermissions($credentials['permissions']);

        return ResponseHelper::jsonResponse(
            new RoleResource($role),
            __('messages.role.updated'),
            201,
            true
        );
    }
    public function list() {

        $admin = auth()->user();

        if (!$admin->hasPermissionTo('عرض ادوار')) {
            throw new PermissionException();
        }

        $roles = Role::with('permissions')->get();

        return ResponseHelper::jsonResponse(
            RoleResource::collection($roles),
            __('messages.role.listed'),
            200,
            true
        );
    }
    public function getRole($id)
    {
        $admin = auth()->user();

        if (!$admin->hasPermissionTo('عرض دور')) {
            throw new PermissionException();
        }

        $role = Role::with('permissions')->find($id);

        if (!$role) {
            throw new RoleNotFoundException();
        }

        return ResponseHelper::jsonResponse(
            new RoleResource($role),
            __('messages.role.get'),
            200,
            true
        );
    }
    public function delete($id)
    {
        $admin = auth()->user();

        /*if (!$admin->hasPermissionTo('حذف دور')) {
            throw new PermissionException();
        }*/

        $role = Role::Find($id);

        if (!$role) {
            throw new RoleNotFoundException();
        }

        $role->delete();

        return ResponseHelper::jsonResponse(
            null,
            __('messages.role.deleted'),
            200,
            true
        );
    }
}
