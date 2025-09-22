<?php

namespace App\Enums\Permissions;

use App\Traits\EnumHelper;

enum TeacherSectionSubjectPermission: string
{
    use EnumHelper;
    
    // Teacher Section Subject Management
    case VIEW_TEACHER_SECTION_SUBJECTS = 'عرض مواد الأساتذة';
    case CREATE_TEACHER_SECTION_SUBJECT = 'انشاء مادة أستاذ';
    case VIEW_TEACHER_SECTION_SUBJECT = 'عرض مادة أستاذ';
    case UPDATE_TEACHER_SECTION_SUBJECT = 'تعديل مادة أستاذ';
    case DELETE_TEACHER_SECTION_SUBJECT = 'حذف مادة أستاذ';
    case MANAGE_DELETED_TEACHER_SECTION_SUBJECTS = 'إدارة مواد الأساتذة المحذوفة';
}
