<?php

use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::get('students', [StudentController::class, 'show']);
    Route::get('students/by-section-semester', [StudentController::class, 'getBySectionAndSemester']);
})->middleware(['user_type:admin', 'throttle:5,1']);
