<?php

namespace App\Enums\Permissions;

use App\Traits\EnumHelper;

enum ExamPermission: string
{
    use EnumHelper;
    
    // Exam Management
    case VIEW_EXAMS = 'عرض الامتحانات';
    case CREATE_EXAM = 'انشاء امتحان';
    case VIEW_EXAM = 'عرض امتحان';
    case UPDATE_EXAM = 'تعديل امتحان';
    case DELETE_EXAM = 'حذف امتحان';
    case MANAGE_DELETED_EXAMS = 'إدارة الامتحانات المحذوفة';
}
