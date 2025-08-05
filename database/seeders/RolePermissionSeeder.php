<?php

namespace Database\Seeders;

use App\Enums\PermissionEnum;
use App\Enums\Permissions\FilesPermission;
use App\Enums\Permissions\NewsPermission;
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

        $superAdminRole = Role::Create(['name' => 'super_admin', 'guard_name' => $guard]);

        // Use the enum to get all permissions
        $permissions = PermissionEnum::getAllPermissions();
        $permissions = [
            ...$permissions,
            'انشاء فترة دوام',
            ...NewsPermission::values(),
            ...FilesPermission::values(),
        ];

        foreach ($permissions as $permission) {
            Permission::create(["name" => $permission, 'guard_name' => $guard,]);
        }

        $superAdminRole->syncPermissions(Permission::all());
    }
}
