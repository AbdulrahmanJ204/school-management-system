<?php

namespace App\Enums\Permissions;

use App\Traits\EnumHelper;

enum ClassSessionPermission: string
{
    use EnumHelper;
    
    // Class Session Management
    case VIEW_CLASS_SESSIONS = 'عرض جلسات الفصول';
    case CREATE_CLASS_SESSION = 'انشاء جلسة فصل';
    case VIEW_CLASS_SESSION = 'عرض جلسة فصل';
    case UPDATE_CLASS_SESSION = 'تعديل جلسة فصل';
    case DELETE_CLASS_SESSION = 'حذف جلسة فصل';
}
