<?php

namespace App\Enums\Permissions;

use App\Traits\EnumHelper;

enum TeacherAttendancePermission: string
{
    use EnumHelper;
    
    // Teacher Attendance Management
    case VIEW_TEACHER_ATTENDANCES = 'عرض حضور المعلمين';
    case CREATE_TEACHER_ATTENDANCE = 'إضافة حضور المعلمين';
    case VIEW_TEACHER_ATTENDANCE = 'عرض حضور المعلم';
    case UPDATE_TEACHER_ATTENDANCE = 'تعديل حضور المعلمين';
    case DELETE_TEACHER_ATTENDANCE = 'حذف حضور المعلمين';
}
