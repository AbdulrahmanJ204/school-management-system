<?php

use App\Http\Controllers\GradeYearSettingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:api', 'user_type:admin', 'throttle:60,1'])->group(function () {
    Route::prefix('grade-year-settings')->group(function () {
        Route::get('/trashed', [GradeYearSettingController::class, 'trashed']);
        Route::patch('/{id}/restore', [GradeYearSettingController::class, 'restore']);
        Route::delete('/{id}/force-delete', [GradeYearSettingController::class, 'forceDelete']);
        Route::get('/grade/{gradeId}', [GradeYearSettingController::class, 'getByGrade']);
        Route::get('/year/{yearId}', [GradeYearSettingController::class, 'getByYear']);
    });
    
    // CRUD routes for grade-year-settings
    Route::get('grade-year-settings', [GradeYearSettingController::class, 'index']);
    Route::post('grade-year-settings', [GradeYearSettingController::class, 'store']);
    Route::get('grade-year-settings/{grade_year_setting}', [GradeYearSettingController::class, 'show']);
    Route::put('grade-year-settings/{grade_year_setting}', [GradeYearSettingController::class, 'update']);
    Route::delete('grade-year-settings/{grade_year_setting}', [GradeYearSettingController::class, 'destroy']);
});
