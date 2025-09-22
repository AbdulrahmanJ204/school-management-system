<?php

use App\Http\Controllers\GradeController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:api', 'user_type:admin', 'throttle:60,1'])->group(function () {
    Route::prefix('grades')->group(function () {
        Route::get('/trashed', [GradeController::class, 'trashed']);
        Route::patch('/{id}/restore', [GradeController::class, 'restore']);
        Route::delete('/{id}/force-delete', [GradeController::class, 'forceDelete']);
    });
    
    // CRUD routes for grades
    Route::get('grades', [GradeController::class, 'index']);
    Route::post('grades', [GradeController::class, 'store']);
    Route::get('grades/{grade}', [GradeController::class, 'show']);
    Route::put('grades/{grade}', [GradeController::class, 'update']);
    Route::delete('grades/{grade}', [GradeController::class, 'destroy']);
});
