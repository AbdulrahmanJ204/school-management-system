<?php

namespace App\Enums\Permissions;

use App\Traits\EnumHelper;

enum SemesterPermission: string
{
    use EnumHelper;
    
    // Semester Management
    case VIEW_SEMESTERS = 'عرض الفصول الدراسية';
    case CREATE_SEMESTER = 'انشاء فصل دراسي';
    case VIEW_SEMESTER = 'عرض فصل دراسي';
    case UPDATE_SEMESTER = 'تعديل فصل دراسي';
    case DELETE_SEMESTER = 'حذف فصل دراسي';
    case MANAGE_DELETED_SEMESTERS = 'إدارة الفصول الدراسية المحذوفة';
}
