<?php

namespace App\Enums\Permissions;

use App\Traits\EnumHelper;

enum SubjectPermission: string
{
    use EnumHelper;
    
    // Subject Management
    case VIEW_SUBJECTS = 'عرض المواد';
    case UPDATE_SUBJECTS = 'تعديل المواد';
    case CREATE_SUBJECTS = 'انشاء المواد';
    case DELETE_SUBJECTS = 'حذف المواد';
    case MANAGE_DELETED_SUBJECTS = 'إدارة المواد المحذوفة';
}
