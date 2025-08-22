<?php

use App\Http\Controllers\SectionController;
use Illuminate\Support\Facades\Route;

Route::prefix('sections')->group(function () {
    Route::middleware(['auth:api', 'user_type:admin', 'throttle:5,1'])->group(function () {
        Route::get('/trashed', [SectionController::class, 'trashed']);
        Route::apiResource('/', SectionController::class);
        Route::patch('/{id}/restore', [SectionController::class, 'restore']);
        Route::delete('/{id}/force-delete', [SectionController::class, 'forceDelete']);
    });
});
