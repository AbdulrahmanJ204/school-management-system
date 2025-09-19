<?php

namespace App\Enums\Permissions;

use App\Traits\EnumHelper;

enum GradePermission: string
{
    use EnumHelper;
    
    // Grade Management
    case VIEW_GRADES = 'عرض الصفوف';
    case CREATE_GRADE = 'انشاء صف';
    case VIEW_GRADE = 'عرض صف';
    case UPDATE_GRADE = 'تعديل صف';
    case DELETE_GRADE = 'حذف صف';
    case MANAGE_DELETED_GRADES = 'إدارة الصفوف المحذوفة';
}
