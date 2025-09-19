<?php

namespace App\Enums\Permissions;

use App\Traits\EnumHelper;

enum StudentAttendancePermission: string
{
    use EnumHelper;
    
    // Student Attendance Management
    case VIEW_STUDENT_ATTENDANCES = 'عرض حضور الطلاب';
    case CREATE_STUDENT_ATTENDANCE = 'إضافة حضور الطلاب';
    case VIEW_STUDENT_ATTENDANCE = 'عرض حضور الطالب';
    case UPDATE_STUDENT_ATTENDANCE = 'تعديل حضور الطلاب';
    case DELETE_STUDENT_ATTENDANCE = 'حذف حضور الطلاب';
}
