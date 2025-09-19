<?php

namespace App\Enums\Permissions;

use App\Traits\EnumHelper;

enum MainSubjectPermission: string
{
    use EnumHelper;
    
    // Main Subject Management
    case VIEW_MAIN_SUBJECTS = 'عرض المواد الرئيسية';
    case CREATE_MAIN_SUBJECT = 'انشاء مادة رئيسية';
    case VIEW_MAIN_SUBJECT = 'عرض مادة رئيسية';
    case UPDATE_MAIN_SUBJECT = 'تعديل مادة رئيسية';
    case DELETE_MAIN_SUBJECT = 'حذف مادة رئيسية';
    case MANAGE_DELETED_MAIN_SUBJECTS = 'إدارة المواد الرئيسية المحذوفة';
}
