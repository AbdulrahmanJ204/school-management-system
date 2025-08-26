<?php

use App\Http\Controllers\AppUpdateController;
use Illuminate\Support\Facades\Route;

// Admin routes for managing app updates
Route::middleware(['auth:api', 'user_type:admin', 'throttle:60,1'])->prefix('admin')->group(function () {
    Route::apiResource('app-updates', AppUpdateController::class);
    Route::get('app-updates/trashed/list', [AppUpdateController::class, 'listTrashed'])->name('app-updates.trashed.list');
    Route::post('app-updates/{id}/restore', [AppUpdateController::class, 'restore'])->name('app-updates.restore');
    Route::delete('app-updates/{id}/force', [AppUpdateController::class, 'forceDelete'])->name('app-updates.force-delete');
});

// User routes for checking app updates (Teachers and Students)
Route::middleware(['auth:api', 'user_type:teacher|student', 'throttle:60,1'])->group(function () {
    Route::post('app-updates/check', [AppUpdateController::class, 'check'])->name('app-updates.check');
});
