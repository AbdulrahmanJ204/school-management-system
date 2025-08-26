<?php

use App\Http\Controllers\BehaviorNoteController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::get('behavior-notes/trashed', [BehaviorNoteController::class, 'trashed']);
    Route::apiResource('behavior-notes', BehaviorNoteController::class);
    Route::patch('behavior-notes/{id}/restore', [BehaviorNoteController::class, 'restore']);
    Route::delete('behavior-notes/{id}/force-delete', [BehaviorNoteController::class, 'forceDelete']);
});
