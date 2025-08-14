<?php

use App\Http\Controllers\GradeController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::get('grades/trashed', [GradeController::class, 'trashed']);
    Route::apiResource('grades', GradeController::class);
    Route::patch('grades/{id}/restore', [GradeController::class, 'restore']);
    Route::delete('grades/{id}/force-delete', [GradeController::class, 'forceDelete']);
});
