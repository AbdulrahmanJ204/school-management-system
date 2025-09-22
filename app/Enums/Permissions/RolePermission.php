<?php

namespace App\Enums\Permissions;

use App\Traits\EnumHelper;

enum RolePermission: string
{
    use EnumHelper;
    
    // Role Management
    case CREATE_ROLE = 'انشاء دور';
    case VIEW_PERMISSIONS = 'عرض الصلاحيات';
    case UPDATE_ROLE = 'تعديل دور';
    case VIEW_ROLES = 'عرض ادوار';
    case VIEW_ROLE = 'عرض دور';
    case DELETE_ROLE = 'حذف دور';
}
