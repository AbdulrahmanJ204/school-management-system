<?php

use App\Http\Controllers\SemesterController;
use Illuminate\Support\Facades\Route;

Route::prefix('semesters')->group(function () {
    Route::middleware(['auth:api', 'user_type:admin', 'throttle:5,1'])->group(function () {
        Route::post('/', [SemesterController::class, 'store']);
        Route::put('/{semester}', [SemesterController::class, 'update']);
        Route::delete('/{semester}', [SemesterController::class, 'destroy']);
        Route::patch('/{semester}/active', [SemesterController::class, 'Active']);
        Route::patch('/{id}/restore', [SemesterController::class, 'restore']);
        Route::delete('/{id}/force-delete', [SemesterController::class, 'forceDelete']);
    });
});
