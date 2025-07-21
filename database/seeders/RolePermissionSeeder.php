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
            'create_quiz',
            'activate_quiz',
            'deactivate_quiz',
            'update_quiz',
            'delete_quiz',
            'create_question',
            'update_question',
            'delete_question',
            'create_news',
            'update_news',
            'delete_news'
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
        $adminRole->givePermissionTo(['create_news']);
        $adminRole->givePermissionTo(['update_news']);
        $adminRole->givePermissionTo(['delete_news']);

        $teacherRole->givePermissionTo(['change_password']);
        $teacherRole->givePermissionTo(['create_quiz']);
        $teacherRole->givePermissionTo(['activate_quiz']);
        $teacherRole->givePermissionTo(['deactivate_quiz']);
        $teacherRole->givePermissionTo(['update_quiz']);
        $teacherRole->givePermissionTo(['delete_quiz']);
        $teacherRole->givePermissionTo(['create_question']);
        $teacherRole->givePermissionTo(['update_question']);
        $teacherRole->givePermissionTo(['delete_question']);
    }
}
