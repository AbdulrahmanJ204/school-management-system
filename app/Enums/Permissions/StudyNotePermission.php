<?php

namespace App\Enums\Permissions;

use App\Traits\EnumHelper;

enum StudyNotePermission: string
{
    use EnumHelper;
    
    // Study Notes Management
    case VIEW_STUDY_NOTES = 'عرض الملاحظات الدراسية';
    case CREATE_STUDY_NOTE = 'انشاء ملاحظة دراسية';
    case VIEW_STUDY_NOTE = 'عرض ملاحظة دراسية';
    case UPDATE_STUDY_NOTE = 'تعديل ملاحظة دراسية';
    case DELETE_STUDY_NOTE = 'حذف ملاحظة دراسية';
    case MANAGE_DELETED_STUDY_NOTES = 'إدارة الملاحظات الدراسية المحذوفة';
}
