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
            'انشاء مستخدم',
            'تعديل مستخدم',
            'عرض المشرفين',
            'عرض الاساتذة',
            'عرض الطلاب',
            'عرض المشرفين و الاساتذة',
            'عرض مستخدم',
            'حذف مستخدم',
            'تغيير كلمة السر',
            'انشاء اختبار مؤتمت',
            'تفعيل اختبار مؤتمت',
            'تعطيل اختبار مؤتمت',
            'تعديل اختبار مؤتمت',
            'حذف اختبار مؤتمت',
            'انشاء سؤال',
            'تعديل سؤال',
            'حذف سؤال',
            'انشاء نتيجة اختبار مؤتمت',
            'عرض الاختبارات المؤتمتة',
            'عرض الاختبار المؤتمت',
            'انشاء دور',
            'عرض الصلاحيات',
            'تعديل دور',
            'عرض ادوار',
            'عرض دور',
            'حذف دور',
            'انشاء فترة دوام',
            ...array_map(fn($permission) => $permission->value, NewsPermission::cases()),
            ...array_map(fn($permission) => $permission->value, FilesPermission::cases()),
        ];

        foreach ($permissions as $permission) {
            Permission::create(["name" => $permission, 'guard_name' => $guard,]);
        }

        $superAdminRole->syncPermissions(Permission::all());
    }
}
