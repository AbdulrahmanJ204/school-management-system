<?php

namespace App\Traits;

use App\Enums\PermissionEnum;
use App\Exceptions\PermissionException;

trait HasPermissionChecks
{
    /**
     * Check if the authenticated user has a specific permission
     * @throws PermissionException
     */
    protected function checkPermission(PermissionEnum $permission): void
    {
        if (!auth()->user()->hasPermissionTo($permission->value)) {
            throw new PermissionException();
        }
    }

    /**
     * Check if the authenticated user has any of the given permissions
     * @throws PermissionException
     */
    protected function checkAnyPermission(array $permissions): void
    {
        $user = auth()->user();
        $hasPermission = false;

        foreach ($permissions as $permission) {
            if ($user->hasPermissionTo($permission->value)) {
                $hasPermission = true;
                break;
            }
        }

        if (!$hasPermission) {
            throw new PermissionException();
        }
    }

    /**
     * Check if the authenticated user has all of the given permissions
     * @throws PermissionException
     */
    protected function checkAllPermissions(array $permissions): void
    {
        $user = auth()->user();

        foreach ($permissions as $permission) {
            if (!$user->hasPermissionTo($permission->value)) {
                throw new PermissionException();
            }
        }
    }
}
