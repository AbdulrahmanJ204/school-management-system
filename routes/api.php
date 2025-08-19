<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\SchoolDayController;
use App\Http\Controllers\SchoolShiftController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ScoreQuizController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->name('auth.')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->name('login')->middleware('throttle:10,1');
    Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot-password')->middleware('throttle:5,1');
    Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('reset-password')->middleware('throttle:5,1');
    Route::middleware(['auth:api'])->group(function () {
        Route::post('refresh', [AuthController::class, 'refresh'])->name('refresh');
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    });
});

Route::middleware('auth:api')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::get('admins', [AdminController::class, 'show']);
    Route::get('teachers', [TeacherController::class, 'show']);
    Route::get('staff', [UserController::class, 'getStaff'])->name('staff');
    Route::resource('users', UserController::class)->only(['show', 'destroy']);
    Route::post('users/{user}', [UserController::class, 'update']);
    Route::resource('roles', RoleController::class);
    Route::get('permissions', [PermissionController::class, 'show']);
    Route::resource('school_shifts', SchoolShiftController::class);
})->middleware(['user_type:admin', 'throttle:5,1']);

Route::middleware('auth:api')->group(function () {
    Route::post('change-password', [AuthController::class, 'changePassword'])->name('change-password');
    })->middleware(['user_type:admin|teacher', 'throttle:5,1']);

Route::middleware('auth:api')->group(function () {
    Route::apiResource('quizzes', QuizController::class);
    Route::put('quizzes/{id}/activate', [QuizController::class, 'activate']);
    Route::put('quizzes/{id}/deactivate', [QuizController::class, 'deactivate']);
    Route::post('quizzes/{quiz_id}/questions', [QuestionController::class, 'create']);
    Route::post('quizzes/{quiz_id}/questions/{question_id}', [QuestionController::class, 'update']);
    Route::delete('quizzes/{quiz_id}/questions/{question_id}', [QuestionController::class, 'destroy']);
    Route::get('quizzes', [QuizController::class, 'index']);
    Route::get('quiz/{id}', [QuizController::class, 'show']);
})->middleware(['user_type:teacher', 'throttle:5,1']);

Route::middleware('auth:api')->group(function () {
    Route::post('score-quizzes', [ScoreQuizController::class, 'create']);
})->middleware(['user_type:student', 'throttle:5,1']);

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
require __DIR__.'/teacher-section-subjects.php';
require __DIR__.'/study-notes.php';
require __DIR__.'/behavior-notes.php';
require __DIR__.'/exams.php';
require __DIR__.'/complaints.php';
require __DIR__.'/messages.php';
require __DIR__.'/class-sessions.php';
require __DIR__.'/student-attendances.php';
require __DIR__.'/teacher-attendances.php';
require __DIR__.'/assignments.php';
require __DIR__.'/logs.php';