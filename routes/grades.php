<?php

use App\Http\Controllers\GradeController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:api', 'user_type:admin', 'throttle:60,1'])->group(function () {
    Route::prefix('grades')->group(function () {
        Route::get('/trashed', [GradeController::class, 'trashed']);
        Route::patch('/{id}/restore', [GradeController::class, 'restore']);
        Route::delete('/{id}/force-delete', [GradeController::class, 'forceDelete']);
    });
    Route::apiResource('grades', GradeController::class);
});
