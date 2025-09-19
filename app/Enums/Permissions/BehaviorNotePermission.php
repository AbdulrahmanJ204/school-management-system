<?php

namespace App\Enums\Permissions;

use App\Traits\EnumHelper;

enum BehaviorNotePermission: string
{
    use EnumHelper;
    
    // Behavior Notes Management
    case VIEW_BEHAVIOR_NOTES = 'عرض ملاحظات السلوك';
    case CREATE_BEHAVIOR_NOTE = 'انشاء ملاحظة سلوك';
    case VIEW_BEHAVIOR_NOTE = 'عرض ملاحظة سلوك';
    case UPDATE_BEHAVIOR_NOTE = 'تعديل ملاحظة سلوك';
    case DELETE_BEHAVIOR_NOTE = 'حذف ملاحظة سلوك';
    case MANAGE_DELETED_BEHAVIOR_NOTES = 'إدارة ملاحظات السلوك المحذوفة';
}
