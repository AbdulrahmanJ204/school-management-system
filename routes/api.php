<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\SchoolDayController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\StudentController;
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
    Route::resource('users', UserController::class)->only(['show', 'destroy']);
    Route::post('users/{user}', [UserController::class, 'update']);
})->middleware(['role:admin', 'throttle:5,1']);

Route::middleware('auth:api')->group(function () {
    Route::post('change-password', [AuthController::class, 'changePassword'])->name('change-password');
})->middleware(['role:admin|teacher', 'throttle:5,1']);

Route::middleware('auth:api')->group(function () {

    Route::prefix('years')->group(function () {
        Route::get('/', [YearController::class, 'index']);
        Route::post('/', [YearController::class, 'store']);
        Route::get('/{year}', [YearController::class, 'show']);
        Route::put('/{year}', [YearController::class, 'update']);
        Route::delete('/{year}', [YearController::class, 'destroy']);
        Route::patch('/{year}/active', [YearController::class, 'Active']);
    });

    Route::prefix('semesters')->group(function () {
        Route::post('/', [SemesterController::class, 'store']);
        Route::put('/{semester}', [SemesterController::class, 'update']);
        Route::delete('/{semester}', [SemesterController::class, 'destroy']);
        Route::patch('/{semester}/active', [SemesterController::class, 'Active']);
    });

    Route::prefix('school-days')->group(function () {
        Route::get('/', [SchoolDayController::class, 'index']);
        Route::post('/', [SchoolDayController::class, 'store']);
//        todo after (behaviorNotes, behaviorNotes, assignments, studentAttendances, teacherAttendances, news)
//        Route::get('/{schoolDay}', [SchoolDayController::class, 'show']);
        Route::put('/{schoolDay}', [SchoolDayController::class, 'update']);
        Route::delete('/{schoolDay}', [SchoolDayController::class, 'destroy']);
    });

    Route::apiResource('grades', GradeController::class);
    Route::apiResource('sections', SectionController::class);
});
