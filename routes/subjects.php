<?php

use App\Http\Controllers\SubjectController;
use App\Http\Controllers\MainSubjectController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    // Main Subjects
    Route::get('main-subjects/trashed', [MainSubjectController::class, 'trashed']);
    Route::apiResource('main-subjects', MainSubjectController::class);
    Route::post('main-subjects/with-subject', [MainSubjectController::class, 'createWithSubject']);
    Route::patch('main-subjects/{id}/restore', [MainSubjectController::class, 'restore']);
    Route::delete('main-subjects/{id}/force-delete', [MainSubjectController::class, 'forceDelete']);

    // Subjects
    Route::get('subjects/trashed', [SubjectController::class, 'trashed']);
    Route::apiResource('subjects', SubjectController::class);
    Route::patch('subjects/{id}/restore', [SubjectController::class, 'restore']);
    Route::delete('subjects/{id}/force-delete', [SubjectController::class, 'forceDelete']);
});
