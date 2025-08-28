<?php

use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\TeacherAssignmentController;
use App\Http\Controllers\StudentAssignmentController;
use Illuminate\Support\Facades\Route;

// Teacher assignment routes
Route::middleware(['auth:api', 'user_type:teacher'])->prefix('teacher')->group(function () {
    Route::prefix('assignments')->controller(TeacherAssignmentController::class)->group(function () {
        Route::post('/', 'store')->name('teacher.assignments.store');
        Route::put('/{id}', 'update')->name('teacher.assignments.update');
        Route::get('/', 'index')->name('teacher.assignments.index');
        Route::delete('/{id}', 'destroy')->name('teacher.assignments.destroy');
    });
});

// Student assignment routes
Route::middleware(['auth:api', 'user_type:student'])->prefix('student')->group(function () {
    Route::prefix('assignments')->controller(StudentAssignmentController::class)->group(function () {
        Route::get('/', 'index')->name('student.assignments.index');
    });
});

// General assignment routes (existing)
Route::prefix('assignments')
    ->controller(AssignmentController::class)
    ->middleware('auth:api')
    ->group(function () {
        Route::get('/', 'index');
        Route::get('/{assignment}', 'show');

        Route::post('/store', 'store');
        Route::post('/restore/{assignment}', 'restore');
        Route::post('/{assignment}', 'update');

        Route::delete('/delete/{assignment}', 'delete');
        Route::delete('/{assignment}', 'destroy');
    });
