<?php

use App\Http\Controllers\StudentMarkController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::apiResource('student-marks', StudentMarkController::class);
    Route::get('student-marks/enrollment/{enrollmentId}', [StudentMarkController::class, 'getByEnrollment']);
    Route::get('student-marks/subject/{subjectId}', [StudentMarkController::class, 'getBySubject']);
    Route::get('student-marks/subject/{subjectId}/section/{sectionId}', [StudentMarkController::class, 'getBySubjectAndSection']);
}); 