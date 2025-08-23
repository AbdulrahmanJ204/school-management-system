<?php

use App\Http\Controllers\StudyNoteController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::get('study-notes/trashed', [StudyNoteController::class, 'trashed']);
    Route::apiResource('study-notes', StudyNoteController::class);
    Route::patch('study-notes/{id}/restore', [StudyNoteController::class, 'restore']);
    Route::delete('study-notes/{id}/force-delete', [StudyNoteController::class, 'forceDelete']);
    Route::get('combined-notes', [StudyNoteController::class, 'getCombinedNotes']);
});
