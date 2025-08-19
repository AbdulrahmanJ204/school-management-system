<?php

namespace Database\Seeders;

use App\Enums\PermissionEnum;
use App\Enums\Permissions\FilesPermission;
use App\Enums\Permissions\NewsPermission;
use App\Enums\Permissions\TimetablePermission;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $guard = 'api';

        $superAdminRole = Role::Create(['name' => 'Owner', 'guard_name' => $guard]);

        // Use the enum to get all permissions
        $permissions = PermissionEnum::getAllPermissions();
        $permissions = [
            ...$permissions,
            ...NewsPermission::values(),
            ...FilesPermission::values(),
            ...TimetablePermission::values(),
        ];

        foreach ($permissions as $permission) {
            Permission::create(["name" => $permission, 'guard_name' => $guard,]);
        }

        $superAdminRole->syncPermissions(Permission::all());
    }
}
