<?php

namespace App\Enums\Permissions;

use App\Traits\EnumHelper;

enum ComplaintPermission: string
{
    use EnumHelper;
    
    // Complaint Management
    case VIEW_COMPLAINTS = 'عرض الشكاوى';
    case CREATE_COMPLAINT = 'انشاء شكوى';
    case VIEW_COMPLAINT = 'عرض شكوى';
    case UPDATE_COMPLAINT = 'تعديل شكوى';
    case DELETE_COMPLAINT = 'حذف شكوى';
    case MANAGE_DELETED_COMPLAINTS = 'إدارة الشكاوى المحذوفة';
}
