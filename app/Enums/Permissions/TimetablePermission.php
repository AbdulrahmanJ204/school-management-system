<?php

namespace App\Enums\Permissions;

use App\Traits\EnumHelper;

enum TimetablePermission : string {
    use EnumHelper;
    case create = 'انشاء فترة دوام';
    case update = 'تعديل فترة دوام';
    case delete = 'حذف فترة دوام';
    case get = 'عرض فترة دوام';
    case list = 'عرض فترات دوام';
    case create_timetable = 'انشاء جدول زمني';
    case update_timetable = 'تعديل جدول زمني';
    case delete_timetable = 'حذف جدول زمني';
    case get_timetable = 'عرض جدول زمني';
    case list_timetable = 'عرض الجداول الزمنية';
    case create_class_period = 'انشاء حصة دراسية';
    case update_class_period = 'تعديل حصة دراسية';
    case delete_class_period = 'حذف حصة دراسية';
    case get_class_period = 'عرض حصة دراسية';
    case list_class_period = 'عرض حصص دراسية';
}
