<?php

use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::get('students', [StudentController::class, 'show']);
    Route::get('students/by-section-semester', [StudentController::class, 'getBySectionAndSemester']);
})->middleware(['user_type:admin', 'throttle:5,1']);

// Student routes - for student app
Route::middleware('auth:api')->group(function () {
    Route::get('student/profile', [StudentController::class, 'getMyProfile']);
})->middleware(['user_type:student', 'throttle:10,1']);