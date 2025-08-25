<?php

use App\Http\Controllers\StudentMarkController;
use Illuminate\Support\Facades\Route;

Route::prefix('student-marks')->group(function () {
    Route::middleware(['auth:api', 'user_type:admin', 'throttle:60,1'])->group(function () {
        Route::apiResource('/', StudentMarkController::class);
        Route::get('/enrollment/{enrollmentId}', [StudentMarkController::class, 'getByEnrollment']);
        Route::get('/subject/{subjectId}', [StudentMarkController::class, 'getBySubject']);
    });
    Route::middleware(['auth:api', 'user_type:teacher', 'throttle:60,1'])->group(function () {
        Route::get('/subject/{subjectId}/section/{sectionId}', [StudentMarkController::class, 'getBySubjectAndSection']);
    });
    Route::middleware(['auth:api', 'user_type:student', 'throttle:60,1'])->group(function () {
        Route::get('/my-marks/{semesterId}', [StudentMarkController::class, 'getMyMarks']);
    });
});
