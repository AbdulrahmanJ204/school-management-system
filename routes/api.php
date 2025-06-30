<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
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
    Route::get('students', [StudentController::class, 'show']);
    Route::resource('users', UserController::class)->only(['show', 'destroy']);
    Route::post('users/{user}', [UserController::class, 'update']);
    Route::post('change-password', [AuthController::class, 'changePassword'])->name('change-password')->middleware('role:student');
})->middleware(['role:admin', 'throttle:5,1']);

