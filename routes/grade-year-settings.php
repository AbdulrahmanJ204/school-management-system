<?php

use App\Http\Controllers\GradeYearSettingController;
use Illuminate\Support\Facades\Route;

Route::prefix('grade-year-settings')->group(function () {
    Route::middleware(['auth:api', 'user_type:admin', 'throttle:60,1'])->group(function () {
        Route::get('/trashed', [GradeYearSettingController::class, 'trashed']);
        Route::patch('/{id}/restore', [GradeYearSettingController::class, 'restore']);
        Route::delete('/{id}/force-delete', [GradeYearSettingController::class, 'forceDelete']);
        Route::get('/grade/{gradeId}', [GradeYearSettingController::class, 'getByGrade']);
        Route::get('/year/{yearId}', [GradeYearSettingController::class, 'getByYear']);
    });
});
Route::apiResource('grade-year-settings', GradeYearSettingController::class);
