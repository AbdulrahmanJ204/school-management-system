<?php

use App\Http\Controllers\GradeYearSettingController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::get('grade-year-settings/trashed', [GradeYearSettingController::class, 'trashed']);
    Route::apiResource('grade-year-settings', GradeYearSettingController::class);
    Route::patch('grade-year-settings/{id}/restore', [GradeYearSettingController::class, 'restore']);
    Route::delete('grade-year-settings/{id}/force-delete', [GradeYearSettingController::class, 'forceDelete']);
    Route::get('grade-year-settings/grade/{gradeId}', [GradeYearSettingController::class, 'getByGrade']);
    Route::get('grade-year-settings/year/{yearId}', [GradeYearSettingController::class, 'getByYear']);
});
