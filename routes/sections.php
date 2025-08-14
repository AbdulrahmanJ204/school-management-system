<?php

use App\Http\Controllers\SectionController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::get('sections/trashed', [SectionController::class, 'trashed']);
    Route::apiResource('sections', SectionController::class);
    Route::patch('sections/{id}/restore', [SectionController::class, 'restore']);
    Route::delete('sections/{id}/force-delete', [SectionController::class, 'forceDelete']);
});
