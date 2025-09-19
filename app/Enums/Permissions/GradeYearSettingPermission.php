<?php

namespace App\Enums\Permissions;

use App\Traits\EnumHelper;

enum GradeYearSettingPermission: string
{
    use EnumHelper;
    
    // Grade Year Settings
    case VIEW_GRADE_YEAR_SETTINGS = 'عرض إعدادات الصفوف السنوية';
    case CREATE_GRADE_YEAR_SETTING = 'انشاء إعداد صف سنوي';
    case VIEW_GRADE_YEAR_SETTING = 'عرض إعداد صف سنوي';
    case UPDATE_GRADE_YEAR_SETTING = 'تعديل إعداد صف سنوي';
    case DELETE_GRADE_YEAR_SETTING = 'حذف إعداد صف سنوي';
    case MANAGE_DELETED_GRADE_YEAR_SETTINGS = 'إدارة إعدادات الصفوف السنوية المحذوفة';
}
