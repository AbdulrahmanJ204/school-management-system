<?php

namespace Database\Seeders;

use App\Enums\PermissionEnum;
use App\Enums\Permissions\FilesPermission;
use App\Enums\Permissions\NewsPermission;
use App\Enums\Permissions\TimetablePermission;
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

        // Use the enum to get all permissions
        $permissions = PermissionEnum::getAllPermissions();
        $permissions = [
            ...$permissions,
            ...NewsPermission::values(),
            ...FilesPermission::values(),
            ...TimetablePermission::values(),
        ];

        foreach ($permissions as $permission) {
            Permission::create(["name" => $permission, 'guard_name' => $guard,]);
        }

        // Assign teacher-specific permissions
        $teacherPermissions = [
            // User Management - Teachers can view students and teachers
            PermissionEnum::VIEW_STUDENTS->value,
            PermissionEnum::VIEW_TEACHERS->value,
            PermissionEnum::VIEW_USER->value,
            PermissionEnum::CHANGE_PASSWORD->value,
            
            // Quiz Management - Teachers can create and manage quizzes
            PermissionEnum::CREATE_AUTOMATED_QUIZ->value,
            PermissionEnum::ACTIVATE_AUTOMATED_QUIZ->value,
            PermissionEnum::DEACTIVATE_AUTOMATED_QUIZ->value,
            PermissionEnum::UPDATE_AUTOMATED_QUIZ->value,
            PermissionEnum::DELETE_AUTOMATED_QUIZ->value,
            PermissionEnum::CREATE_QUESTION->value,
            PermissionEnum::UPDATE_QUESTION->value,
            PermissionEnum::DELETE_QUESTION->value,
            PermissionEnum::CREATE_QUIZ_RESULT->value,
            PermissionEnum::VIEW_AUTOMATED_QUIZZES->value,
            PermissionEnum::VIEW_AUTOMATED_QUIZ->value,
            
            // Student Marks - Teachers can manage student marks
            PermissionEnum::VIEW_STUDENT_MARKS->value,
            PermissionEnum::CREATE_STUDENT_MARK->value,
            PermissionEnum::VIEW_STUDENT_MARK->value,
            PermissionEnum::UPDATE_STUDENT_MARK->value,
            PermissionEnum::DELETE_STUDENT_MARK->value,
            
            // Student Enrollments - Teachers can view enrollments
            PermissionEnum::VIEW_STUDENT_ENROLLMENTS->value,
            PermissionEnum::VIEW_STUDENT_ENROLLMENT->value,
            
            // Subjects - Teachers can view subjects
            PermissionEnum::VIEW_SUBJECTS->value,
            PermissionEnum::VIEW_MAIN_SUBJECTS->value,
            PermissionEnum::VIEW_MAIN_SUBJECT->value,
            
            // Teacher Section Subject - Teachers can view their assignments
            PermissionEnum::VIEW_TEACHER_SECTION_SUBJECTS->value,
            PermissionEnum::VIEW_TEACHER_SECTION_SUBJECT->value,
            
            // Study Notes - Teachers can manage study notes
            PermissionEnum::VIEW_STUDY_NOTES->value,
            PermissionEnum::CREATE_STUDY_NOTE->value,
            PermissionEnum::VIEW_STUDY_NOTE->value,
            PermissionEnum::UPDATE_STUDY_NOTE->value,
            PermissionEnum::DELETE_STUDY_NOTE->value,
            
            // Behavior Notes - Teachers can manage behavior notes
            PermissionEnum::VIEW_BEHAVIOR_NOTES->value,
            PermissionEnum::CREATE_BEHAVIOR_NOTE->value,
            PermissionEnum::VIEW_BEHAVIOR_NOTE->value,
            PermissionEnum::UPDATE_BEHAVIOR_NOTE->value,
            PermissionEnum::DELETE_BEHAVIOR_NOTE->value,
            
            // Exams - Teachers can manage exams
            PermissionEnum::VIEW_EXAMS->value,
            PermissionEnum::CREATE_EXAM->value,
            PermissionEnum::VIEW_EXAM->value,
            PermissionEnum::UPDATE_EXAM->value,
            PermissionEnum::DELETE_EXAM->value,
            
            // Complaints - Teachers can manage complaints
            PermissionEnum::VIEW_COMPLAINTS->value,
            PermissionEnum::CREATE_COMPLAINT->value,
            PermissionEnum::VIEW_COMPLAINT->value,
            PermissionEnum::UPDATE_COMPLAINT->value,
            PermissionEnum::DELETE_COMPLAINT->value,
            
            // Messages - Teachers can manage messages
            PermissionEnum::VIEW_MESSAGES->value,
            PermissionEnum::CREATE_MESSAGE->value,
            PermissionEnum::VIEW_MESSAGE->value,
            PermissionEnum::UPDATE_MESSAGE->value,
            PermissionEnum::DELETE_MESSAGE->value,
            
            // Class Sessions - Teachers can manage class sessions
            PermissionEnum::VIEW_CLASS_SESSIONS->value,
            PermissionEnum::CREATE_CLASS_SESSION->value,
            PermissionEnum::VIEW_CLASS_SESSION->value,
            PermissionEnum::UPDATE_CLASS_SESSION->value,
            PermissionEnum::DELETE_CLASS_SESSION->value,
            
            // Student Attendance - Teachers can manage student attendance
            PermissionEnum::VIEW_STUDENT_ATTENDANCES->value,
            PermissionEnum::CREATE_STUDENT_ATTENDANCE->value,
            PermissionEnum::VIEW_STUDENT_ATTENDANCE->value,
            PermissionEnum::UPDATE_STUDENT_ATTENDANCE->value,
            PermissionEnum::DELETE_STUDENT_ATTENDANCE->value,
            
            // Teacher Attendance - Teachers can view their own attendance
            PermissionEnum::VIEW_TEACHER_ATTENDANCES->value,
            PermissionEnum::VIEW_TEACHER_ATTENDANCE->value,
            
            // Assignments - Teachers can manage assignments
            PermissionEnum::VIEW_ASSIGNMENTS->value,
            PermissionEnum::CREATE_ASSIGNMENT->value,
            PermissionEnum::VIEW_ASSIGNMENT->value,
            PermissionEnum::UPDATE_ASSIGNMENT->value,
            PermissionEnum::DELETE_ASSIGNMENT->value,
            
            // News - Teachers can create and manage news/announcements
            NewsPermission::create->value,
            NewsPermission::update->value,
            NewsPermission::softDelete->value,
            NewsPermission::restore->value,
            
            // Files - Teachers can manage educational files
            FilesPermission::store->value,
            FilesPermission::update->value,
            FilesPermission::softDelete->value,
            FilesPermission::restore->value,
            FilesPermission::download->value,
            
            // Timetable - Teachers can view timetables and schedules
            TimetablePermission::get->value,
            TimetablePermission::list->value,
            TimetablePermission::get_timetable->value,
            TimetablePermission::list_timetable->value,
            TimetablePermission::get_class_period->value,
            TimetablePermission::list_class_period->value,
            TimetablePermission::get_schedule->value,
            TimetablePermission::list_schedule->value,
        ];

        // Assign student-specific permissions
        $studentPermissions = [
            // User Management - Students can view their own profile and change password
            PermissionEnum::VIEW_USER->value,
            PermissionEnum::CHANGE_PASSWORD->value,
            
            // Quiz Management - Students can take quizzes and view results
            PermissionEnum::VIEW_AUTOMATED_QUIZZES->value,
            PermissionEnum::VIEW_AUTOMATED_QUIZ->value,
            PermissionEnum::CREATE_QUIZ_RESULT->value,
            
            // Student Marks - Students can view their own marks
            PermissionEnum::VIEW_STUDENT_MARKS->value,
            PermissionEnum::VIEW_STUDENT_MARK->value,
            
            // Student Enrollments - Students can view their own enrollment
            PermissionEnum::VIEW_STUDENT_ENROLLMENT->value,
            
            // Subjects - Students can view subjects they're enrolled in
            PermissionEnum::VIEW_SUBJECTS->value,
            PermissionEnum::VIEW_MAIN_SUBJECTS->value,
            PermissionEnum::VIEW_MAIN_SUBJECT->value,
            
            // Teacher Section Subject - Students can view their teachers
            PermissionEnum::VIEW_TEACHER_SECTION_SUBJECTS->value,
            PermissionEnum::VIEW_TEACHER_SECTION_SUBJECT->value,
            
            // Study Notes - Students can view study notes
            PermissionEnum::VIEW_STUDY_NOTES->value,
            PermissionEnum::VIEW_STUDY_NOTE->value,
            
            // Behavior Notes - Students can view their own behavior notes
            PermissionEnum::VIEW_BEHAVIOR_NOTES->value,
            PermissionEnum::VIEW_BEHAVIOR_NOTE->value,
            
            // Exams - Students can view exams
            PermissionEnum::VIEW_EXAMS->value,
            PermissionEnum::VIEW_EXAM->value,
            
            // Complaints - Students can create and view their own complaints
            PermissionEnum::VIEW_COMPLAINTS->value,
            PermissionEnum::CREATE_COMPLAINT->value,
            PermissionEnum::VIEW_COMPLAINT->value,
            PermissionEnum::UPDATE_COMPLAINT->value,
            
            // Messages - Students can send and receive messages
            PermissionEnum::VIEW_MESSAGES->value,
            PermissionEnum::CREATE_MESSAGE->value,
            PermissionEnum::VIEW_MESSAGE->value,
            PermissionEnum::UPDATE_MESSAGE->value,
            PermissionEnum::DELETE_MESSAGE->value,
            
            // Class Sessions - Students can view class sessions
            PermissionEnum::VIEW_CLASS_SESSIONS->value,
            PermissionEnum::VIEW_CLASS_SESSION->value,
            
            // Student Attendance - Students can view their own attendance
            PermissionEnum::VIEW_STUDENT_ATTENDANCE->value,
            
            // Assignments - Students can view assignments
            PermissionEnum::VIEW_ASSIGNMENTS->value,
            PermissionEnum::VIEW_ASSIGNMENT->value,
            
            // News - Students can view news/announcements
            NewsPermission::create->value, // Students can view news (assuming this is the view permission)
            
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

        $TeacherRole->syncPermissions($teacherPermissions);
        $StudentRole->syncPermissions($studentPermissions);

        $OwnerRole->syncPermissions(Permission::all());
    }
}
