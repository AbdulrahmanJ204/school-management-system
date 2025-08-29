<?php

use App\Http\Controllers\ClassSessionController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    // CRUD routes for class-sessions
    Route::get('class-sessions', [ClassSessionController::class, 'index']);
    Route::post('class-sessions', [ClassSessionController::class, 'store']);
    Route::get('class-sessions/{class_session}', [ClassSessionController::class, 'show']);
    Route::put('class-sessions/{class_session}', [ClassSessionController::class, 'update']);
    Route::delete('class-sessions/{class_session}', [ClassSessionController::class, 'destroy']);

    Route::patch('class-sessions/{class_session}/cancel', [ClassSessionController::class, 'cancel']);

    Route::get('class-sessions/teacher/{teacherId}', [ClassSessionController::class, 'getByTeacher']);
    Route::get('class-sessions/section/{sectionId}', [ClassSessionController::class, 'getBySection']);
    Route::get('class-sessions/school-day/{schoolDayId}', [ClassSessionController::class, 'getBySchoolDay']);
});
