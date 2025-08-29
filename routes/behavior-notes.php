<?php

use App\Http\Controllers\BehaviorNoteController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::get('behavior-notes/trashed', [BehaviorNoteController::class, 'trashed']);
    
    // CRUD routes for behavior-notes
    Route::get('behavior-notes', [BehaviorNoteController::class, 'index']);
    Route::post('behavior-notes', [BehaviorNoteController::class, 'store']);
    Route::get('behavior-notes/{behavior_note}', [BehaviorNoteController::class, 'show']);
    Route::put('behavior-notes/{behavior_note}', [BehaviorNoteController::class, 'update']);
    Route::delete('behavior-notes/{behavior_note}', [BehaviorNoteController::class, 'destroy']);
    
    Route::patch('behavior-notes/{id}/restore', [BehaviorNoteController::class, 'restore']);
    Route::delete('behavior-notes/{id}/force-delete', [BehaviorNoteController::class, 'forceDelete']);
});
