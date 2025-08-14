<?php

use App\Http\Controllers\ExamController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::get('exams/trashed', [ExamController::class, 'trashed']);
    Route::apiResource('exams', ExamController::class);
    Route::patch('exams/{id}/restore', [ExamController::class, 'restore']);
    Route::delete('exams/{id}/force-delete', [ExamController::class, 'forceDelete']);
    Route::get('exams/school-day/{schoolDayId}', [ExamController::class, 'getBySchoolDay']);
    Route::get('exams/grade/{gradeId}', [ExamController::class, 'getByGrade']);
});
