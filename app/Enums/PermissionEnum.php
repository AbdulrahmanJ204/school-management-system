<?php

namespace App\Enums;

enum PermissionEnum: string
{
    // User Management
    case CREATE_USER = 'انشاء مستخدم';
    case UPDATE_USER = 'تعديل مستخدم';
    case VIEW_ADMINS = 'عرض المشرفين';
    case VIEW_TEACHERS = 'عرض الاساتذة';
    case VIEW_STUDENTS = 'عرض الطلاب';
    case VIEW_ADMINS_AND_TEACHERS = 'عرض المشرفين و الاساتذة';
    case VIEW_USER = 'عرض مستخدم';
    case DELETE_USER = 'حذف مستخدم';
    case CHANGE_PASSWORD = 'تغيير كلمة السر';

    // Quiz Management
    case CREATE_AUTOMATED_QUIZ = 'انشاء اختبار مؤتمت';
    case ACTIVATE_AUTOMATED_QUIZ = 'تفعيل اختبار مؤتمت';
    case DEACTIVATE_AUTOMATED_QUIZ = 'تعطيل اختبار مؤتمت';
    case UPDATE_AUTOMATED_QUIZ = 'تعديل اختبار مؤتمت';
    case DELETE_AUTOMATED_QUIZ = 'حذف اختبار مؤتمت';
    case CREATE_QUESTION = 'انشاء سؤال';
    case UPDATE_QUESTION = 'تعديل سؤال';
    case DELETE_QUESTION = 'حذف سؤال';
    case CREATE_QUIZ_RESULT = 'انشاء نتيجة اختبار مؤتمت';
    case VIEW_AUTOMATED_QUIZZES = 'عرض الاختبارات المؤتمتة';
    case VIEW_AUTOMATED_QUIZ = 'عرض الاختبار المؤتمت';

    // Role Management
    case CREATE_ROLE = 'انشاء دور';
    case VIEW_PERMISSIONS = 'عرض الصلاحيات';
    case UPDATE_ROLE = 'تعديل دور';
    case VIEW_ROLES = 'عرض ادوار';
    case VIEW_ROLE = 'عرض دور';
    case DELETE_ROLE = 'حذف دور';

    // Main Subject Management
    case VIEW_MAIN_SUBJECTS = 'عرض المواد الرئيسية';
    case CREATE_MAIN_SUBJECT = 'انشاء مادة رئيسية';
    case VIEW_MAIN_SUBJECT = 'عرض مادة رئيسية';
    case UPDATE_MAIN_SUBJECT = 'تعديل مادة رئيسية';
    case DELETE_MAIN_SUBJECT = 'حذف مادة رئيسية';
    case MANAGE_DELETED_MAIN_SUBJECTS = 'إدارة المواد الرئيسية المحذوفة';

    // Subject Management
    case VIEW_SUBJECTS = 'عرض المواد';
    case UPDATE_SUBJECTS = 'تعديل المواد';
    case CREATE_SUBJECTS = 'انشاء المواد';
    case DELETE_SUBJECTS = 'حذف المواد';
    case MANAGE_DELETED_SUBJECTS = 'إدارة المواد المحذوفة';

    // Student Marks
    case VIEW_STUDENT_MARKS = 'عرض درجات الطلاب';
    case CREATE_STUDENT_MARK = 'انشاء درجة طالب';
    case VIEW_STUDENT_MARK = 'عرض درجة طالب';
    case UPDATE_STUDENT_MARK = 'تعديل درجة طالب';
    case DELETE_STUDENT_MARK = 'حذف درجة طالب';
    case MANAGE_DELETED_STUDENT_MARKS = 'إدارة درجات الطلاب المحذوفة';

    // Student Enrollments
    case VIEW_STUDENT_ENROLLMENTS = 'عرض تسجيلات الطلاب';
    case CREATE_STUDENT_ENROLLMENT = 'انشاء تسجيل طالب';
    case VIEW_STUDENT_ENROLLMENT = 'عرض تسجيل طالب';
    case UPDATE_STUDENT_ENROLLMENT = 'تعديل تسجيل طالب';
    case DELETE_STUDENT_ENROLLMENT = 'حذف تسجيل طالب';
    case MANAGE_DELETED_STUDENT_ENROLLMENTS = 'إدارة تسجيلات الطلاب المحذوفة';

    // Grade Year Settings
    case VIEW_GRADE_YEAR_SETTINGS = 'عرض إعدادات الصفوف السنوية';
    case CREATE_GRADE_YEAR_SETTING = 'انشاء إعداد صف سنوي';
    case VIEW_GRADE_YEAR_SETTING = 'عرض إعداد صف سنوي';
    case UPDATE_GRADE_YEAR_SETTING = 'تعديل إعداد صف سنوي';
    case DELETE_GRADE_YEAR_SETTING = 'حذف إعداد صف سنوي';
    case MANAGE_DELETED_GRADE_YEAR_SETTINGS = 'إدارة إعدادات الصفوف السنوية المحذوفة';

    // Year Management
    case VIEW_YEARS = 'عرض السنوات الدراسية';
    case CREATE_YEAR = 'انشاء سنة دراسية';
    case VIEW_YEAR = 'عرض سنة دراسية';
    case UPDATE_YEAR = 'تعديل سنة دراسية';
    case DELETE_YEAR = 'حذف سنة دراسية';
    case MANAGE_DELETED_YEARS = 'إدارة السنوات الدراسية المحذوفة';

    // Semester Management
    case VIEW_SEMESTERS = 'عرض الفصول الدراسية';
    case CREATE_SEMESTER = 'انشاء فصل دراسي';
    case VIEW_SEMESTER = 'عرض فصل دراسي';
    case UPDATE_SEMESTER = 'تعديل فصل دراسي';
    case DELETE_SEMESTER = 'حذف فصل دراسي';
    case MANAGE_DELETED_SEMESTERS = 'إدارة الفصول الدراسية المحذوفة';

    // School Day Management
    case VIEW_SCHOOL_DAYS = 'عرض أيام الدراسة';
    case CREATE_SCHOOL_DAY = 'انشاء يوم دراسة';
    case VIEW_SCHOOL_DAY = 'عرض يوم دراسة';
    case UPDATE_SCHOOL_DAY = 'تعديل يوم دراسة';
    case DELETE_SCHOOL_DAY = 'حذف يوم دراسة';
    case MANAGE_DELETED_SCHOOL_DAYS = 'إدارة أيام الدراسة المحذوفة';

    // Grade Management
    case VIEW_GRADES = 'عرض الصفوف';
    case CREATE_GRADE = 'انشاء صف';
    case VIEW_GRADE = 'عرض صف';
    case UPDATE_GRADE = 'تعديل صف';
    case DELETE_GRADE = 'حذف صف';
    case MANAGE_DELETED_GRADES = 'إدارة الصفوف المحذوفة';

    // Section Management
    case VIEW_SECTIONS = 'عرض الشعب';
    case CREATE_SECTION = 'انشاء شعبة';
    case VIEW_SECTION = 'عرض شعبة';
    case UPDATE_SECTION = 'تعديل شعبة';
    case DELETE_SECTION = 'حذف شعبة';
    case MANAGE_DELETED_SECTIONS = 'إدارة الشعب المحذوفة';

    // Teacher Section Subject Management
    case VIEW_TEACHER_SECTION_SUBJECTS = 'عرض مواد الأساتذة';
    case CREATE_TEACHER_SECTION_SUBJECT = 'انشاء مادة أستاذ';
    case VIEW_TEACHER_SECTION_SUBJECT = 'عرض مادة أستاذ';
    case UPDATE_TEACHER_SECTION_SUBJECT = 'تعديل مادة أستاذ';
    case DELETE_TEACHER_SECTION_SUBJECT = 'حذف مادة أستاذ';
    case MANAGE_DELETED_TEACHER_SECTION_SUBJECTS = 'إدارة مواد الأساتذة المحذوفة';

    // Study Notes Management
    case VIEW_STUDY_NOTES = 'عرض الملاحظات الدراسية';
    case CREATE_STUDY_NOTE = 'انشاء ملاحظة دراسية';
    case VIEW_STUDY_NOTE = 'عرض ملاحظة دراسية';
    case UPDATE_STUDY_NOTE = 'تعديل ملاحظة دراسية';
    case DELETE_STUDY_NOTE = 'حذف ملاحظة دراسية';
    case MANAGE_DELETED_STUDY_NOTES = 'إدارة الملاحظات الدراسية المحذوفة';

    // Behavior Notes Management
    case VIEW_BEHAVIOR_NOTES = 'عرض ملاحظات السلوك';
    case CREATE_BEHAVIOR_NOTE = 'انشاء ملاحظة سلوك';
    case VIEW_BEHAVIOR_NOTE = 'عرض ملاحظة سلوك';
    case UPDATE_BEHAVIOR_NOTE = 'تعديل ملاحظة سلوك';
    case DELETE_BEHAVIOR_NOTE = 'حذف ملاحظة سلوك';
    case MANAGE_DELETED_BEHAVIOR_NOTES = 'إدارة ملاحظات السلوك المحذوفة';

    // Exam Management
    case VIEW_EXAMS = 'عرض الامتحانات';
    case CREATE_EXAM = 'انشاء امتحان';
    case VIEW_EXAM = 'عرض امتحان';
    case UPDATE_EXAM = 'تعديل امتحان';
    case DELETE_EXAM = 'حذف امتحان';
    case MANAGE_DELETED_EXAMS = 'إدارة الامتحانات المحذوفة';

    // Complaint Management
    case VIEW_COMPLAINTS = 'عرض الشكاوى';
    case CREATE_COMPLAINT = 'انشاء شكوى';
    case VIEW_COMPLAINT = 'عرض شكوى';
    case UPDATE_COMPLAINT = 'تعديل شكوى';
    case DELETE_COMPLAINT = 'حذف شكوى';
    case MANAGE_DELETED_COMPLAINTS = 'إدارة الشكاوى المحذوفة';

    // Messages Management
    case VIEW_MESSAGES = 'عرض الرسائل';
    case CREATE_MESSAGE = 'انشاء رسالة';
    case VIEW_MESSAGE = 'عرض رسالة';
    case UPDATE_MESSAGE = 'تعديل رسالة';
    case DELETE_MESSAGE = 'حذف رسالة';
    case MANAGE_DELETED_MESSAGES = 'إدارة الرسائل المحذوفة';

    // Class Session Management
    case VIEW_CLASS_SESSIONS = 'عرض جلسات الفصول';
    case CREATE_CLASS_SESSION = 'انشاء جلسة فصل';
    case VIEW_CLASS_SESSION = 'عرض جلسة فصل';
    case UPDATE_CLASS_SESSION = 'تعديل جلسة فصل';
    case DELETE_CLASS_SESSION = 'حذف جلسة فصل';

    // Student Attendance Management
    case VIEW_STUDENT_ATTENDANCES = 'عرض حضور الطلاب';
    case CREATE_STUDENT_ATTENDANCE = 'إضافة حضور الطلاب';
    case VIEW_STUDENT_ATTENDANCE = 'عرض حضور الطالب';
    case UPDATE_STUDENT_ATTENDANCE = 'تعديل حضور الطلاب';
    case DELETE_STUDENT_ATTENDANCE = 'حذف حضور الطلاب';

    // Teacher Attendance Management
    case VIEW_TEACHER_ATTENDANCES = 'عرض حضور المعلمين';
    case CREATE_TEACHER_ATTENDANCE = 'إضافة حضور المعلمين';
    case VIEW_TEACHER_ATTENDANCE = 'عرض حضور المعلم';
    case UPDATE_TEACHER_ATTENDANCE = 'تعديل حضور المعلمين';
    case DELETE_TEACHER_ATTENDANCE = 'حذف حضور المعلمين';

    // Assignment Management
    case VIEW_ASSIGNMENTS = 'عرض الواجبات';
    case CREATE_ASSIGNMENT = 'انشاء واجب';
    case VIEW_ASSIGNMENT = 'عرض واجب';
    case UPDATE_ASSIGNMENT = 'تعديل واجب';
    case DELETE_ASSIGNMENT = 'حذف واجب';
    case MANAGE_DELETED_ASSIGNMENTS = 'إدارة الواجبات المحذوفة';

    /**
     * Get all permissions as an array
     */
    public static function getAllPermissions(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get permissions for a specific module
     */
    public static function getModulePermissions(string $module): array
    {
        return match ($module) {
            'year' => [
                self::VIEW_YEARS->value,
                self::CREATE_YEAR->value,
                self::VIEW_YEAR->value,
                self::UPDATE_YEAR->value,
                self::DELETE_YEAR->value,
                self::MANAGE_DELETED_YEARS->value,
            ],
            'semester' => [
                self::VIEW_SEMESTERS->value,
                self::CREATE_SEMESTER->value,
                self::VIEW_SEMESTER->value,
                self::UPDATE_SEMESTER->value,
                self::DELETE_SEMESTER->value,
                self::MANAGE_DELETED_SEMESTERS->value,
            ],
            'school_day' => [
                self::VIEW_SCHOOL_DAYS->value,
                self::CREATE_SCHOOL_DAY->value,
                self::VIEW_SCHOOL_DAY->value,
                self::UPDATE_SCHOOL_DAY->value,
                self::DELETE_SCHOOL_DAY->value,
                self::MANAGE_DELETED_SCHOOL_DAYS->value,
            ],
            'grade' => [
                self::VIEW_GRADES->value,
                self::CREATE_GRADE->value,
                self::VIEW_GRADE->value,
                self::UPDATE_GRADE->value,
                self::DELETE_GRADE->value,
                self::MANAGE_DELETED_GRADES->value,
            ],
            'section' => [
                self::VIEW_SECTIONS->value,
                self::CREATE_SECTION->value,
                self::VIEW_SECTION->value,
                self::UPDATE_SECTION->value,
                self::DELETE_SECTION->value,
                self::MANAGE_DELETED_SECTIONS->value,
            ],
            'main_subject' => [
                self::VIEW_MAIN_SUBJECTS->value,
                self::CREATE_MAIN_SUBJECT->value,
                self::VIEW_MAIN_SUBJECT->value,
                self::UPDATE_MAIN_SUBJECT->value,
                self::DELETE_MAIN_SUBJECT->value,
                self::MANAGE_DELETED_MAIN_SUBJECTS->value,
            ],
            'subject' => [
                self::VIEW_SUBJECTS->value,
                self::CREATE_SUBJECTS->value,
                self::UPDATE_SUBJECTS->value,
                self::DELETE_SUBJECTS->value,
                self::MANAGE_DELETED_SUBJECTS->value,
            ],
            'student_enrollment' => [
                self::VIEW_STUDENT_ENROLLMENTS->value,
                self::CREATE_STUDENT_ENROLLMENT->value,
                self::VIEW_STUDENT_ENROLLMENT->value,
                self::UPDATE_STUDENT_ENROLLMENT->value,
                self::DELETE_STUDENT_ENROLLMENT->value,
                self::MANAGE_DELETED_STUDENT_ENROLLMENTS->value,
            ],
            'grade_year_setting' => [
                self::VIEW_GRADE_YEAR_SETTINGS->value,
                self::CREATE_GRADE_YEAR_SETTING->value,
                self::VIEW_GRADE_YEAR_SETTING->value,
                self::UPDATE_GRADE_YEAR_SETTING->value,
                self::DELETE_GRADE_YEAR_SETTING->value,
                self::MANAGE_DELETED_GRADE_YEAR_SETTINGS->value,
            ],
            'student_mark' => [
                self::VIEW_STUDENT_MARKS->value,
                self::CREATE_STUDENT_MARK->value,
                self::VIEW_STUDENT_MARK->value,
                self::UPDATE_STUDENT_MARK->value,
                self::DELETE_STUDENT_MARK->value,
                self::MANAGE_DELETED_STUDENT_MARKS->value,
            ],
            'teacher_section_subject' => [
                self::VIEW_TEACHER_SECTION_SUBJECTS->value,
                self::CREATE_TEACHER_SECTION_SUBJECT->value,
                self::VIEW_TEACHER_SECTION_SUBJECT->value,
                self::UPDATE_TEACHER_SECTION_SUBJECT->value,
                self::DELETE_TEACHER_SECTION_SUBJECT->value,
                self::MANAGE_DELETED_TEACHER_SECTION_SUBJECTS->value,
            ],
            'study_note' => [
                self::VIEW_STUDY_NOTES->value,
                self::CREATE_STUDY_NOTE->value,
                self::VIEW_STUDY_NOTE->value,
                self::UPDATE_STUDY_NOTE->value,
                self::DELETE_STUDY_NOTE->value,
                self::MANAGE_DELETED_STUDY_NOTES->value,
            ],
            'behavior_note' => [
                self::VIEW_BEHAVIOR_NOTES->value,
                self::CREATE_BEHAVIOR_NOTE->value,
                self::VIEW_BEHAVIOR_NOTE->value,
                self::UPDATE_BEHAVIOR_NOTE->value,
                self::DELETE_BEHAVIOR_NOTE->value,
                self::MANAGE_DELETED_BEHAVIOR_NOTES->value,
            ],
            'exam' => [
                self::VIEW_EXAMS->value,
                self::CREATE_EXAM->value,
                self::VIEW_EXAM->value,
                self::UPDATE_EXAM->value,
                self::DELETE_EXAM->value,
                self::MANAGE_DELETED_EXAMS->value,
            ],
            'complaint' => [
                self::VIEW_COMPLAINTS->value,
                self::CREATE_COMPLAINT->value,
                self::VIEW_COMPLAINT->value,
                self::UPDATE_COMPLAINT->value,
                self::DELETE_COMPLAINT->value,
                self::MANAGE_DELETED_COMPLAINTS->value,
            ],
            'message' => [
                self::VIEW_MESSAGES->value,
                self::CREATE_MESSAGE->value,
                self::VIEW_MESSAGE->value,
                self::UPDATE_MESSAGE->value,
                self::DELETE_MESSAGE->value,
                self::MANAGE_DELETED_MESSAGES->value,
            ],
            'class_session' => [
                self::VIEW_CLASS_SESSIONS->value,
                self::CREATE_CLASS_SESSION->value,
                self::VIEW_CLASS_SESSION->value,
                self::UPDATE_CLASS_SESSION->value,
                self::DELETE_CLASS_SESSION->value,
            ],
            'student_attendance' => [
                self::VIEW_STUDENT_ATTENDANCES->value,
                self::CREATE_STUDENT_ATTENDANCE->value,
                self::VIEW_STUDENT_ATTENDANCE->value,
                self::UPDATE_STUDENT_ATTENDANCE->value,
                self::DELETE_STUDENT_ATTENDANCE->value,
            ],
            'teacher_attendance' => [
                self::VIEW_TEACHER_ATTENDANCES->value,
                self::CREATE_TEACHER_ATTENDANCE->value,
                self::VIEW_TEACHER_ATTENDANCE->value,
                self::UPDATE_TEACHER_ATTENDANCE->value,
                self::DELETE_TEACHER_ATTENDANCE->value,
            ],
            'assignment' => [
                self::VIEW_ASSIGNMENTS->value,
                self::CREATE_ASSIGNMENT->value,
                self::VIEW_ASSIGNMENT->value,
                self::UPDATE_ASSIGNMENT->value,
                self::DELETE_ASSIGNMENT->value,
                self::MANAGE_DELETED_ASSIGNMENTS->value,
            ],
            default => [],
        };
    }
} 