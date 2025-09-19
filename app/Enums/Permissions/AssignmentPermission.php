<?php

namespace App\Enums\Permissions;

use App\Traits\EnumHelper;

enum AssignmentPermission: string
{
    use EnumHelper;
    
    // Assignment Management
    case VIEW_ASSIGNMENTS = 'عرض الواجبات';
    case CREATE_ASSIGNMENT = 'انشاء واجب';
    case VIEW_ASSIGNMENT = 'عرض واجب';
    case UPDATE_ASSIGNMENT = 'تعديل واجب';
    case DELETE_ASSIGNMENT = 'حذف واجب';
    case MANAGE_DELETED_ASSIGNMENTS = 'إدارة الواجبات المحذوفة';
}
