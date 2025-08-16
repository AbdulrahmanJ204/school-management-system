<?php

use App\Http\Controllers\ClassSessionController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::apiResource('class-sessions', ClassSessionController::class);

    Route::patch('class-sessions/{class_session}/cancel', [ClassSessionController::class, 'cancel']);

    Route::get('class-sessions/teacher/{teacherId}', [ClassSessionController::class, 'getByTeacher']);
    Route::get('class-sessions/section/{sectionId}', [ClassSessionController::class, 'getBySection']);
    Route::get('class-sessions/school-day/{schoolDayId}', [ClassSessionController::class, 'getBySchoolDay']);
});
