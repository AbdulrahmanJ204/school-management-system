<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassPeriodController;
use App\Http\Controllers\ClassSessionController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SchoolShiftController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ScoreQuizController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TimeTableController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->name('auth.')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->name('login')->middleware('throttle:60,1');
    Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot-password')->middleware('throttle:60,1');
    Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('reset-password')->middleware('throttle:60,1');
    Route::middleware(['auth:api'])->group(function () {
        Route::post('refresh', [AuthController::class, 'refresh'])->name('refresh');
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    });
});

Route::middleware(['auth:api', 'user_type:admin', 'throttle:60,1'])->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::get('admins', [AdminController::class, 'show'])->name('admins');
    Route::get('teachers', [TeacherController::class, 'show'])->name('teachers');
    Route::get('staff', [UserController::class, 'getStaff'])->name('staff');
    Route::resource('users', UserController::class)->only(['show', 'destroy']);
    Route::post('users/{user}', [UserController::class, 'update'])->name('user.update');;
    Route::resource('roles', RoleController::class);
    Route::get('permissions', [PermissionController::class, 'show']);
    Route::resource('school_shifts', SchoolShiftController::class);
    Route::resource('timetable', TimeTableController::class);
    Route::resource('class_period', ClassPeriodController::class);
    Route::resource('schedules', ScheduleController::class);
    Route::post('class-sessions', [ClassSessionController::class, 'create']);
    Route::put('class-sessions/{id}', [ClassSessionController::class, 'update']);
    Route::delete('class-sessions/{id}', [ClassSessionController::class, 'destroy']);
});

Route::middleware(['auth:api', 'user_type:admin|teacher', 'throttle:60,1'])->group(function () {
    Route::post('change-password', [AuthController::class, 'changePassword'])->name('change-password');
});

Route::middleware(['auth:api', 'user_type:admin|teacher', 'throttle:60,1'])->group(function () {
    Route::apiResource('quizzes', QuizController::class)->except(['update']);
    Route::post('quizzes/{id}/update', [QuizController::class, 'update']);
    Route::put('quizzes/{id}/activate', [QuizController::class, 'activate']);
    Route::put('quizzes/{id}/deactivate', [QuizController::class, 'deactivate']);
    Route::post('quizzes/{quiz_id}/questions', [QuestionController::class, 'create']);
    Route::post('quizzes/{quiz_id}/questions/{question_id}', [QuestionController::class, 'update']);
    Route::delete('quizzes/{quiz_id}/questions/{question_id}', [QuestionController::class, 'destroy']);
    Route::get('quizzes', [QuizController::class, 'index']);
    Route::get('quiz/{id}', [QuizController::class, 'show']);
    Route::get('teacher/grades-sections-subjects', [TeacherController::class, 'getGradesSectionsSubjects'])->name('teacher.grades-sections-subjects');
    Route::get('teacher/section/{sectionId}/subject/{subjectId}/students', [TeacherController::class, 'getStudentsInSectionWithMarks'])->name('teacher.section.students');
    Route::get('teacher/profile', [TeacherController::class, 'getProfile'])->name('teacher.profile');
});

Route::middleware(['auth:api', 'user_type:student', 'throttle:60,1'])->group(function () {
    Route::post('score-quizzes', [ScoreQuizController::class, 'create']);
});

require __DIR__.'/news.php';
require __DIR__.'/files.php';
require __DIR__.'/years.php';
require __DIR__.'/semesters.php';
require __DIR__.'/school-days.php';
require __DIR__.'/grades.php';
require __DIR__.'/sections.php';
require __DIR__.'/subjects.php';
require __DIR__.'/students.php';
require __DIR__.'/student-enrollments.php';
require __DIR__.'/grade-year-settings.php';
require __DIR__.'/student-marks.php';

// Todo: Add User_type middleware to next Api's
require __DIR__.'/teacher-section-subjects.php';
require __DIR__.'/study-notes.php';
require __DIR__.'/study-notes-api.php';
require __DIR__.'/behavior-notes.php';
require __DIR__.'/behavior-notes-api.php';
require __DIR__.'/exams.php';
require __DIR__.'/complaints.php';
require __DIR__.'/messages.php';
require __DIR__.'/class-sessions.php';
require __DIR__.'/student-attendances.php';
require __DIR__.'/teacher-attendances.php';
require __DIR__.'/assignments.php';
require __DIR__.'/logs.php';

