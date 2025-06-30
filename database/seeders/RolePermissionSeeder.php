<?php

namespace Database\Seeders;

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

        $adminRole = Role::Create(['name' => 'admin', 'guard_name' => $guard]);
        $studentRole = Role::Create(['name' => 'student', 'guard_name' => $guard]);
        $teacherRole = Role::Create(['name' => 'teacher', 'guard_name' => $guard]);

        $permissions = [
            'create_user',
            'update_user',
            'list_admins',
            'list_teachers',
            'list_students',
            'get_user',
            'delete_user',
            'change_password',
        ];

        foreach ($permissions as $permission) {
            Permission::create(["name" => $permission, 'guard_name' => $guard,]);
        }

        $adminRole->givePermissionTo(['create_user']);
        $adminRole->givePermissionTo(['update_user']);
        $adminRole->givePermissionTo(['list_admins']);
        $adminRole->givePermissionTo(['list_teachers']);
        $adminRole->givePermissionTo(['list_students']);
        $adminRole->givePermissionTo(['get_user']);
        $adminRole->givePermissionTo(['change_password']);
        $adminRole->givePermissionTo(['delete_user']);

        $teacherRole->givePermissionTo(['change_password']);
    }
}
