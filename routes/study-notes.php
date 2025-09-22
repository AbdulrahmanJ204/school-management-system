<?php

use App\Http\Controllers\StudyNoteController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::get('study-notes/trashed', [StudyNoteController::class, 'trashed']);
    
    // CRUD routes for study-notes
    Route::get('study-notes', [StudyNoteController::class, 'index']);
    Route::post('study-notes', [StudyNoteController::class, 'store']);
    Route::get('study-notes/{study_note}', [StudyNoteController::class, 'show']);
    Route::put('study-notes/{study_note}', [StudyNoteController::class, 'update']);
    Route::delete('study-notes/{study_note}', [StudyNoteController::class, 'destroy']);
    
    Route::patch('study-notes/{id}/restore', [StudyNoteController::class, 'restore']);
    Route::delete('study-notes/{id}/force-delete', [StudyNoteController::class, 'forceDelete']);
    Route::get('combined-notes', [StudyNoteController::class, 'getCombinedNotes']);
});
