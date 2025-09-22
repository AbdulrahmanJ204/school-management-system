<?php

namespace App\Enums\Permissions;

use App\Traits\EnumHelper;

enum SchoolDayPermission: string
{
    use EnumHelper;
    
    // School Day Management
    case VIEW_SCHOOL_DAYS = 'عرض أيام الدراسة';
    case CREATE_SCHOOL_DAY = 'انشاء يوم دراسة';
    case VIEW_SCHOOL_DAY = 'عرض يوم دراسة';
    case UPDATE_SCHOOL_DAY = 'تعديل يوم دراسة';
    case DELETE_SCHOOL_DAY = 'حذف يوم دراسة';
    case MANAGE_DELETED_SCHOOL_DAYS = 'إدارة أيام الدراسة المحذوفة';
}
