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
    
    // CRUD routes for main-subjects
    Route::get('main-subjects', [MainSubjectController::class, 'index']);
    Route::post('main-subjects', [MainSubjectController::class, 'store']);
    Route::get('main-subjects/{main_subject}', [MainSubjectController::class, 'show']);
    Route::put('main-subjects/{main_subject}', [MainSubjectController::class, 'update']);
    Route::delete('main-subjects/{main_subject}', [MainSubjectController::class, 'destroy']);
});

Route::middleware(['auth:api', 'user_type:admin', 'throttle:60,1'])->group(function () {
Route::prefix('subjects')->group(function () {
        Route::get('/trashed', [SubjectController::class, 'trashed']);
        Route::patch('/{id}/restore', [SubjectController::class, 'restore']);
        Route::delete('/{id}/force-delete', [SubjectController::class, 'forceDelete']);
    });
    
    // CRUD routes for subjects
    Route::get('subjects', [SubjectController::class, 'index']);
    Route::post('subjects', [SubjectController::class, 'store']);
    Route::get('subjects/{subject}', [SubjectController::class, 'show']);
    Route::put('subjects/{subject}', [SubjectController::class, 'update']);
    Route::delete('subjects/{subject}', [SubjectController::class, 'destroy']);
});
