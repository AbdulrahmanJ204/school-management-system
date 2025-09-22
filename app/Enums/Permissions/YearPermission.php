<?php

namespace App\Enums\Permissions;

use App\Traits\EnumHelper;

enum YearPermission: string
{
    use EnumHelper;
    
    // Year Management
    case VIEW_YEARS = 'عرض السنوات الدراسية';
    case CREATE_YEAR = 'انشاء سنة دراسية';
    case VIEW_YEAR = 'عرض سنة دراسية';
    case UPDATE_YEAR = 'تعديل سنة دراسية';
    case DELETE_YEAR = 'حذف سنة دراسية';
    case MANAGE_DELETED_YEARS = 'إدارة السنوات الدراسية المحذوفة';
}
