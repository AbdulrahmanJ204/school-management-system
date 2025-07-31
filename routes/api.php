<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\SchoolDayController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ScoreQuizController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentMarkController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\SubjectMajorController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\YearController;
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
    Route::get('students', [StudentController::class, 'show']);
    Route::get('staff', [UserController::class, 'getStaff']);
    Route::resource('users', UserController::class)->only(['show', 'destroy']);
    Route::post('users/{user}', [UserController::class, 'update']);
    Route::resource('roles', RoleController::class);
    Route::get('permissions', [PermissionController::class, 'show']);
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

Route::middleware('auth:api')->group(function () {

    Route::prefix('years')->group(function () {
        Route::get('/', [YearController::class, 'index']);
        Route::get('/trashed', [YearController::class, 'trashed']);
        Route::post('/', [YearController::class, 'store']);
        Route::get('/{year}', [YearController::class, 'show']);
        Route::put('/{year}', [YearController::class, 'update']);
        Route::delete('/{year}', [YearController::class, 'destroy']);
        Route::patch('/{year}/active', [YearController::class, 'Active']);
        Route::patch('/{id}/restore', [YearController::class, 'restore']);
        Route::delete('/{id}/force-delete', [YearController::class, 'forceDelete']);
    });

    Route::prefix('semesters')->group(function () {
        Route::get('/trashed', [SemesterController::class, 'trashed']);
        Route::post('/', [SemesterController::class, 'store']);
        Route::put('/{semester}', [SemesterController::class, 'update']);
        Route::delete('/{semester}', [SemesterController::class, 'destroy']);
        Route::patch('/{semester}/active', [SemesterController::class, 'Active']);
        Route::patch('/{id}/restore', [SemesterController::class, 'restore']);
        Route::delete('/{id}/force-delete', [SemesterController::class, 'forceDelete']);
    });

    Route::prefix('school-days')->group(function () {
        Route::get('/trashed', [SchoolDayController::class, 'trashed']);
        Route::get('/{semester}', [SchoolDayController::class, 'index']);
        Route::post('/', [SchoolDayController::class, 'store']);
//        todo after (behaviorNotes, behaviorNotes, assignments, studentAttendances, teacherAttendances, news)
//        Route::get('/{schoolDay}', [SchoolDayController::class, 'show']);
        Route::put('/{schoolDay}', [SchoolDayController::class, 'update']);
        Route::delete('/{schoolDay}', [SchoolDayController::class, 'destroy']);
        Route::patch('/{id}/restore', [SchoolDayController::class, 'restore']);
        Route::delete('/{id}/force-delete', [SchoolDayController::class, 'forceDelete']);
    });

    Route::apiResource('grades', GradeController::class);
    Route::get('grades/trashed', [GradeController::class, 'trashed']);
    Route::patch('grades/{id}/restore', [GradeController::class, 'restore']);
    Route::delete('grades/{id}/force-delete', [GradeController::class, 'forceDelete']);
    
    Route::apiResource('sections', SectionController::class);
    Route::get('sections/trashed', [SectionController::class, 'trashed']);
    Route::patch('sections/{id}/restore', [SectionController::class, 'restore']);
    Route::delete('sections/{id}/force-delete', [SectionController::class, 'forceDelete']);
    
    Route::apiResource('main-subjects', SubjectMajorController::class);
    Route::get('main-subjects/trashed', [MainSubjectController::class, 'trashed']);
    Route::patch('main-subjects/{id}/restore', [MainSubjectController::class, 'restore']);
    Route::delete('main-subjects/{id}/force-delete', [MainSubjectController::class, 'forceDelete']);
    
    Route::apiResource('subjects', SubjectController::class);
    Route::get('subjects/trashed', [SubjectController::class, 'trashed']);
    Route::patch('subjects/{id}/restore', [SubjectController::class, 'restore']);
    Route::delete('subjects/{id}/force-delete', [SubjectController::class, 'forceDelete']);
    Route::apiResource('student-marks', StudentMarkController::class);
    Route::get('student-marks/enrollment/{enrollmentId}', [StudentMarkController::class, 'getByEnrollment']);
    Route::get('student-marks/subject/{subjectId}', [StudentMarkController::class, 'getBySubject']);

});

require __DIR__.'/news.php';
require __DIR__.'/files.php';
