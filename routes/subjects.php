<?php

use App\Http\Controllers\SubjectController;
use App\Http\Controllers\MainSubjectController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:api', 'user_type:admin', 'throttle:60,1'])->group(function () {
Route::prefix('main-subjects')->group(function () {
        Route::get('/trashed', [MainSubjectController::class, 'trashed']);
        Route::post('/with-subject', [MainSubjectController::class, 'createWithSubject']);
        Route::patch('/{id}/restore', [MainSubjectController::class, 'restore']);
        Route::delete('/{id}/force-delete', [MainSubjectController::class, 'forceDelete']);
    });
    Route::apiResource('main-subjects', MainSubjectController::class);
});

Route::middleware(['auth:api', 'user_type:admin', 'throttle:60,1'])->group(function () {
Route::prefix('subjects')->group(function () {
        Route::get('/trashed', [SubjectController::class, 'trashed']);
        Route::patch('/{id}/restore', [SubjectController::class, 'restore']);
        Route::delete('/{id}/force-delete', [SubjectController::class, 'forceDelete']);
    });
    Route::apiResource('subjects', SubjectController::class);
});
