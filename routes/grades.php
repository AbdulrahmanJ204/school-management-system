<?php

use App\Http\Controllers\GradeController;
use Illuminate\Support\Facades\Route;

Route::prefix('grades')->group(function () {
    Route::middleware(['auth:api', 'user_type:admin', 'throttle:5,1'])->group(function () {
        Route::get('/trashed', [GradeController::class, 'trashed']);
        Route::apiResource('/', GradeController::class);
        Route::patch('/{id}/restore', [GradeController::class, 'restore']);
        Route::delete('/{id}/force-delete', [GradeController::class, 'forceDelete']);
    });
});
