<?php

use App\Http\Controllers\SectionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:api', 'user_type:admin', 'throttle:60,1'])->group(function () {
    Route::prefix('sections')->group(function () {
        Route::get('/trashed', [SectionController::class, 'trashed']);
        Route::patch('/{id}/restore', [SectionController::class, 'restore']);
        Route::delete('/{id}/force-delete', [SectionController::class, 'forceDelete']);
    });
    Route::apiResource('sections', SectionController::class);
});
