<?php

namespace App\Enums\Permissions;

use App\Traits\EnumHelper;

enum StudentMarkPermission: string
{
    use EnumHelper;
    
    // Student Marks
    case VIEW_STUDENT_MARKS = 'عرض درجات الطلاب';
    case CREATE_STUDENT_MARK = 'انشاء درجة طالب';
    case VIEW_STUDENT_MARK = 'عرض درجة طالب';
    case UPDATE_STUDENT_MARK = 'تعديل درجة طالب';
    case DELETE_STUDENT_MARK = 'حذف درجة طالب';
    case MANAGE_DELETED_STUDENT_MARKS = 'إدارة درجات الطلاب المحذوفة';
    case VIEW_STUDENT_REPORT = 'عرض تقرير الطالب';
}
