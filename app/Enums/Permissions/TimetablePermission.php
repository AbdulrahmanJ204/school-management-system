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
    case create_class_period = 'انشاء فترة دراسية';
    case update_class_period = 'تعديل فترة دراسية';
    case delete_class_period = 'حذف فترة دراسية';
    case get_class_period = 'عرض فترة دراسية';
    case list_class_period = 'عرض فترات دراسية';
    case create_schedule = 'انشاء جدول دراسي';
    case update_schedule = 'تعديل جدول دراسي';
    case delete_schedule = 'حذف جدول دراسي';
    case get_schedule = 'عرض جدول دراسي';
    case list_schedule = 'عرض جداول دراسية';
    case create_class_session = 'انشاء حصة دراسية ';
    case update_class_session = 'تعديل حصة دراسية';
    case delete_class_session = 'حذف حصة دراسية';
    case get_class_session = 'عرض حصة دراسية';
    case list_class_session = 'عرض حصص دراسية';
}
