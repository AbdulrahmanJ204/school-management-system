<?php

namespace App\Enums\Permissions;

use App\Traits\EnumHelper;

enum StudentEnrollmentPermission: string
{
    use EnumHelper;
    
    // Student Enrollments
    case VIEW_STUDENT_ENROLLMENTS = 'عرض تسجيلات الطلاب';
    case CREATE_STUDENT_ENROLLMENT = 'انشاء تسجيل طالب';
    case VIEW_STUDENT_ENROLLMENT = 'عرض تسجيل طالب';
    case UPDATE_STUDENT_ENROLLMENT = 'تعديل تسجيل طالب';
    case DELETE_STUDENT_ENROLLMENT = 'حذف تسجيل طالب';
    case MANAGE_DELETED_STUDENT_ENROLLMENTS = 'إدارة تسجيلات الطلاب المحذوفة';
}
