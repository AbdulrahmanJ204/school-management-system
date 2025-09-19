<?php

namespace App\Enums\Permissions;

use App\Traits\EnumHelper;

enum MessagePermission: string
{
    use EnumHelper;
    
    // Messages Management
    case VIEW_MESSAGES = 'عرض الرسائل';
    case CREATE_MESSAGE = 'انشاء رسالة';
    case VIEW_MESSAGE = 'عرض رسالة';
    case UPDATE_MESSAGE = 'تعديل رسالة';
    case DELETE_MESSAGE = 'حذف رسالة';
    case MANAGE_DELETED_MESSAGES = 'إدارة الرسائل المحذوفة';
}
