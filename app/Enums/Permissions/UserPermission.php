<?php

namespace App\Enums\Permissions;

use App\Traits\EnumHelper;

enum UserPermission: string
{
    use EnumHelper;
    
    // User Management
    case CREATE_USER = 'انشاء مستخدم';
    case UPDATE_USER = 'تعديل مستخدم';
    case VIEW_ADMINS = 'عرض المشرفين';
    case VIEW_TEACHERS = 'عرض الاساتذة';
    case VIEW_STUDENTS = 'عرض الطلاب';
    case VIEW_ADMINS_AND_TEACHERS = 'عرض المشرفين و الاساتذة';
    case VIEW_USER = 'عرض مستخدم';
    case DELETE_USER = 'حذف مستخدم';
    case CHANGE_PASSWORD = 'تغيير كلمة السر';
}
