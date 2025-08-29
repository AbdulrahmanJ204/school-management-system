<?php

use App\Http\Controllers\StudentMarkController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth:api', 'user_type:admin', 'throttle:60,1'])->group(function () {
    Route::apiResource('student-marks', StudentMarkController::class);
});
Route::prefix('student-marks')->group(function () {
    Route::middleware(['auth:api', 'user_type:teacher', 'throttle:60,1'])->group(function () {
        Route::get('/subject/{subjectId}/section/{sectionId}', [StudentMarkController::class, 'getBySubjectAndSection']);
    });
    Route::middleware(['auth:api', 'user_type:student', 'throttle:60,1'])->group(function () {
        Route::get('/my-marks/{semesterId}', [StudentMarkController::class, 'getMyMarks']);
        Route::get('/my-marks', [StudentMarkController::class, 'getMyAllMarks']);
    });
});
