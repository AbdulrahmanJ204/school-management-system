<?php

use App\Http\Controllers\StudentMarkController;
use Illuminate\Support\Facades\Route;

Route::prefix('student-marks')->group(function () {
    Route::middleware(['auth:api', 'user_type:teacher', 'throttle:60,1'])->group(function () {
        Route::get('/subject/{subjectId}/section/{sectionId}', [StudentMarkController::class, 'getBySubjectAndSection']);
    });
    Route::middleware(['auth:api', 'user_type:student', 'throttle:60,1'])->group(function () {
        Route::get('/my-marks/{semesterId}', [StudentMarkController::class, 'getMyMarks']);
        Route::get('/my-marks', [StudentMarkController::class, 'getMyAllMarks']);
    });
});


Route::middleware(['auth:api', 'user_type:admin', 'throttle:60,1'])->group(function () {
    Route::post('student-marks-bulk', [StudentMarkController::class, 'bulkStore'])->name('student-marks.bulk.store');
    Route::put('student-marks-bulk', [StudentMarkController::class, 'bulkUpdate'])->name('student-marks.bulk.update');

    // CRUD routes for student-marks
    Route::get('student-marks', [StudentMarkController::class, 'index']);
    Route::post('student-marks', [StudentMarkController::class, 'store']);
    Route::get('student-marks/{student_mark}', [StudentMarkController::class, 'show']);
    Route::put('student-marks/{student_mark}', [StudentMarkController::class, 'update']);
    Route::delete('student-marks/{student_mark}', [StudentMarkController::class, 'destroy']);
});
