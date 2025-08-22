<?php

use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::prefix('students')->group(function () {
    Route::middleware(['auth:api', 'user_type:admin', 'throttle:5,1'])->group(function () {
        Route::get('/', [StudentController::class, 'show']);
        Route::get('/by-section-semester', [StudentController::class, 'getBySectionAndSemester']);
    });
    Route::middleware(['auth:api', 'user_type:student', 'throttle:5,1'])->group(function () {
        Route::get('student/profile', [StudentController::class, 'getMyProfile']);
    });
});
