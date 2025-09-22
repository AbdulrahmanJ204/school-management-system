<?php

use App\Http\Controllers\SectionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:api', 'user_type:admin', 'throttle:60,1'])->group(function () {
    Route::prefix('sections')->group(function () {
        Route::get('/trashed', [SectionController::class, 'trashed']);
        Route::patch('/{id}/restore', [SectionController::class, 'restore']);
        Route::delete('/{id}/force-delete', [SectionController::class, 'forceDelete']);
    });
    
    // CRUD routes for sections
    Route::get('sections', [SectionController::class, 'index']);
    Route::post('sections', [SectionController::class, 'store']);
    Route::get('sections/{section}', [SectionController::class, 'show']);
    Route::put('sections/{section}', [SectionController::class, 'update']);
    Route::delete('sections/{section}', [SectionController::class, 'destroy']);
});
