<?php

namespace App\Enums;

use App\Enums\Permissions\AssignmentPermission;
use App\Enums\Permissions\BehaviorNotePermission;
use App\Enums\Permissions\ClassSessionPermission;
use App\Enums\Permissions\ComplaintPermission;
use App\Enums\Permissions\ExamPermission;
use App\Enums\Permissions\FilesPermission;
use App\Enums\Permissions\GradePermission;
use App\Enums\Permissions\GradeYearSettingPermission;
use App\Enums\Permissions\MainSubjectPermission;
use App\Enums\Permissions\MessagePermission;
use App\Enums\Permissions\NewsPermission;
use App\Enums\Permissions\QuizPermission;
use App\Enums\Permissions\RolePermission;
use App\Enums\Permissions\SchoolDayPermission;
use App\Enums\Permissions\SectionPermission;
use App\Enums\Permissions\SemesterPermission;
use App\Enums\Permissions\StudentAttendancePermission;
use App\Enums\Permissions\StudentEnrollmentPermission;
use App\Enums\Permissions\StudentMarkPermission;
use App\Enums\Permissions\StudyNotePermission;
use App\Enums\Permissions\SubjectPermission;
use App\Enums\Permissions\TeacherAttendancePermission;
use App\Enums\Permissions\TeacherSectionSubjectPermission;
use App\Enums\Permissions\TimetablePermission;
use App\Enums\Permissions\UserPermission;
use App\Enums\Permissions\YearPermission;

class AllPermissions
{
    /**
     * Get all permissions as an array
     */
    public static function getAllPermissions(): array
    {
        return array_merge(
            UserPermission::values(),
            QuizPermission::values(),
            RolePermission::values(),
            YearPermission::values(),
            SemesterPermission::values(),
            SchoolDayPermission::values(),
            GradePermission::values(),
            SectionPermission::values(),
            GradeYearSettingPermission::values(),
            MainSubjectPermission::values(),
            SubjectPermission::values(),
            TeacherSectionSubjectPermission::values(),
            StudentMarkPermission::values(),
            StudentEnrollmentPermission::values(),
            StudyNotePermission::values(),
            BehaviorNotePermission::values(),
            ExamPermission::values(),
            ComplaintPermission::values(),
            MessagePermission::values(),
            ClassSessionPermission::values(),
            StudentAttendancePermission::values(),
            TeacherAttendancePermission::values(),
            AssignmentPermission::values(),
            NewsPermission::values(),
            FilesPermission::values(),
            TimetablePermission::values()
        );
    }

    /**
     * Get permissions for a specific module
     */
    public static function getModulePermissions(string $module): array
    {
        return match ($module) {
            'user' => UserPermission::values(),
            'quiz' => QuizPermission::values(),
            'role' => RolePermission::values(),
            'year' => YearPermission::values(),
            'semester' => SemesterPermission::values(),
            'school_day' => SchoolDayPermission::values(),
            'grade' => GradePermission::values(),
            'section' => SectionPermission::values(),
            'main_subject' => MainSubjectPermission::values(),
            'subject' => SubjectPermission::values(),
            'student_enrollment' => StudentEnrollmentPermission::values(),
            'grade_year_setting' => GradeYearSettingPermission::values(),
            'student_mark' => StudentMarkPermission::values(),
            'teacher_section_subject' => TeacherSectionSubjectPermission::values(),
            'study_note' => StudyNotePermission::values(),
            'behavior_note' => BehaviorNotePermission::values(),
            'exam' => ExamPermission::values(),
            'complaint' => ComplaintPermission::values(),
            'message' => MessagePermission::values(),
            'class_session' => ClassSessionPermission::values(),
            'student_attendance' => StudentAttendancePermission::values(),
            'teacher_attendance' => TeacherAttendancePermission::values(),
            'assignment' => AssignmentPermission::values(),
            'news' => NewsPermission::values(),
            'files' => FilesPermission::values(),
            'timetable' => TimetablePermission::values(),
            default => [],
        };
    }
}
