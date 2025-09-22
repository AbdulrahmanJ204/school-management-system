<?php

namespace App\Enums\Permissions;

use App\Traits\EnumHelper;

enum SectionPermission: string
{
    use EnumHelper;
    
    // Section Management
    case VIEW_SECTIONS = 'عرض الشعب';
    case CREATE_SECTION = 'انشاء شعبة';
    case VIEW_SECTION = 'عرض شعبة';
    case UPDATE_SECTION = 'تعديل شعبة';
    case DELETE_SECTION = 'حذف شعبة';
    case MANAGE_DELETED_SECTIONS = 'إدارة الشعب المحذوفة';
}
