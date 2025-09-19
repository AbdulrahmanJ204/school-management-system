<?php

namespace Database\Seeders;

use App\Enums\AllPermissions;
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
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $guard = 'api';

        $OwnerRole = Role::Create(['name' => 'Owner', 'guard_name' => $guard]);
        $TeacherRole = Role::Create(['name' => 'Teacher', 'guard_name' => $guard]);
        $StudentRole = Role::Create(['name' => 'Student', 'guard_name' => $guard]);

        // Use the AllPermissions class to get all permissions
        $permissions = AllPermissions::getAllPermissions();

        foreach ($permissions as $permission) {
            Permission::create(["name" => $permission, 'guard_name' => $guard,]);
        }

        // Assign student-specific permissions
        $studentPermissions = [
            // User Management - Students can view their own profile and change password
            UserPermission::VIEW_USER->value,
            UserPermission::CHANGE_PASSWORD->value,

            // Quiz Management - Students can take quizzes and view results
            QuizPermission::VIEW_AUTOMATED_QUIZZES->value,
            QuizPermission::VIEW_AUTOMATED_QUIZ->value,
            QuizPermission::CREATE_QUIZ_RESULT->value,

            // Student Marks - Students can view their own marks
            StudentMarkPermission::VIEW_STUDENT_MARKS->value,
            StudentMarkPermission::VIEW_STUDENT_MARK->value,

            // Student Enrollments - Students can view their own enrollment
            StudentEnrollmentPermission::VIEW_STUDENT_ENROLLMENT->value,

            // Subjects - Students can view subjects they're enrolled in
            SubjectPermission::VIEW_SUBJECTS->value,
            MainSubjectPermission::VIEW_MAIN_SUBJECTS->value,
            MainSubjectPermission::VIEW_MAIN_SUBJECT->value,

            // Teacher Section Subject - Students can view their teachers
            TeacherSectionSubjectPermission::VIEW_TEACHER_SECTION_SUBJECTS->value,
            TeacherSectionSubjectPermission::VIEW_TEACHER_SECTION_SUBJECT->value,

            // Study Notes - Students can view study notes
            StudyNotePermission::VIEW_STUDY_NOTES->value,
            StudyNotePermission::VIEW_STUDY_NOTE->value,

            // Behavior Notes - Students can view their own behavior notes
            BehaviorNotePermission::VIEW_BEHAVIOR_NOTES->value,
            BehaviorNotePermission::VIEW_BEHAVIOR_NOTE->value,

            // Exams - Students can view exams
            ExamPermission::VIEW_EXAMS->value,
            ExamPermission::VIEW_EXAM->value,

            // Complaints - Students can create and view their own complaints
            ComplaintPermission::VIEW_COMPLAINTS->value,
            ComplaintPermission::CREATE_COMPLAINT->value,
            ComplaintPermission::VIEW_COMPLAINT->value,
            ComplaintPermission::UPDATE_COMPLAINT->value,

            // Messages - Students can send and receive messages
            MessagePermission::VIEW_MESSAGES->value,
            MessagePermission::CREATE_MESSAGE->value,
            MessagePermission::VIEW_MESSAGE->value,
            MessagePermission::UPDATE_MESSAGE->value,
            MessagePermission::DELETE_MESSAGE->value,

            // Class Sessions - Students can view class sessions
            ClassSessionPermission::VIEW_CLASS_SESSIONS->value,
            ClassSessionPermission::VIEW_CLASS_SESSION->value,

            // Student Attendance - Students can view their own attendance
            StudentAttendancePermission::VIEW_STUDENT_ATTENDANCE->value,

            // Assignments - Students can view assignments
            AssignmentPermission::VIEW_ASSIGNMENTS->value,
            AssignmentPermission::VIEW_ASSIGNMENT->value,

            // News - Students can view news/announcements
            NewsPermission::ListNews->value,

            // Files - Students can download educational files
            FilesPermission::download->value,

            // Timetable - Students can view their timetables and schedules
            TimetablePermission::get->value,
            TimetablePermission::list->value,
            TimetablePermission::get_timetable->value,
            TimetablePermission::list_timetable->value,
            TimetablePermission::get_class_period->value,
            TimetablePermission::list_class_period->value,
            TimetablePermission::get_schedule->value,
            TimetablePermission::list_schedule->value,
        ];

        $TeacherRole->syncPermissions(Permission::all());
        $StudentRole->syncPermissions($studentPermissions);

        $OwnerRole->syncPermissions(Permission::all());
    }
}
