<?php

use App\Http\Controllers\SchoolDayController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::prefix('school-days')->group(function () {
        Route::get('/{semester}/trashed', [SchoolDayController::class, 'trashed']);
        Route::get('/{semester}', [SchoolDayController::class, 'index']);
        Route::post('/', [SchoolDayController::class, 'store']);
        Route::get('/{schoolDay}', [SchoolDayController::class, 'show']);
        Route::put('/{schoolDay}', [SchoolDayController::class, 'update']);
        Route::delete('/{schoolDay}', [SchoolDayController::class, 'destroy']);
        Route::patch('/{id}/restore', [SchoolDayController::class, 'restore']);
        Route::delete('/{id}/force-delete', [SchoolDayController::class, 'forceDelete']);
    });
});
