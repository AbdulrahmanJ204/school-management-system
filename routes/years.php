<?php

use App\Http\Controllers\YearController;
use Illuminate\Support\Facades\Route;

Route::prefix('years')->group(function () {
    Route::middleware(['auth:api', 'user_type:admin', 'throttle:5,1'])->group(function () {
        Route::get('/', [YearController::class, 'index']);
        Route::get('/trashed', [YearController::class, 'trashed']);
        Route::post('/', [YearController::class, 'store']);
        Route::get('/{year}', [YearController::class, 'show']);
        Route::put('/{year}', [YearController::class, 'update']);
        Route::delete('/{year}', [YearController::class, 'destroy']);
        Route::patch('/{year}/active', [YearController::class, 'Active']);
        Route::patch('/{id}/restore', [YearController::class, 'restore']);
        Route::delete('/{id}/force-delete', [YearController::class, 'forceDelete']);
        Route::get('/with-nested-data', [YearController::class, 'withNestedData']);
    });
    Route::middleware(['auth:api', 'user_type:teacher', 'throttle:5,1'])->group(function () {

    });
});
