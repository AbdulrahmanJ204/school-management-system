<?php

use App\Http\Controllers\SubjectController;
use App\Http\Controllers\MainSubjectController;
use Illuminate\Support\Facades\Route;

Route::prefix('main-subjects')->group(function () {
    Route::middleware(['auth:api', 'user_type:admin', 'throttle:5,1'])->group(function () {
        Route::get('/trashed', [MainSubjectController::class, 'trashed']);
        Route::apiResource('/', MainSubjectController::class);
        Route::post('/with-subject', [MainSubjectController::class, 'createWithSubject']);
        Route::patch('/{id}/restore', [MainSubjectController::class, 'restore']);
        Route::delete('/{id}/force-delete', [MainSubjectController::class, 'forceDelete']);
    });
});

Route::prefix('subjects')->group(function () {
    Route::middleware(['auth:api', 'user_type:admin', 'throttle:5,1'])->group(function () {
        Route::get('/trashed', [SubjectController::class, 'trashed']);
        Route::apiResource('/', SubjectController::class);
        Route::patch('/{id}/restore', [SubjectController::class, 'restore']);
        Route::delete('/{id}/force-delete', [SubjectController::class, 'forceDelete']);
    });
});
