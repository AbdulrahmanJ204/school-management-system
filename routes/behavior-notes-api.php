<?php

use App\Http\Controllers\BehaviorNoteController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:api', 'user_type:student'])->group(function () {
    Route::get('student/behavior-notes', [BehaviorNoteController::class, 'getStudentBehaviorNotes']);
});

Route::middleware(['auth:api', 'user_type:teacher'])->group(function () {
    Route::get('teacher/behavior-notes', [BehaviorNoteController::class, 'getTeacherBehaviorNotes']);
    Route::post('teacher/behavior-notes', [BehaviorNoteController::class, 'createTeacherBehaviorNote']);
    Route::put('teacher/behavior-notes/{id}', [BehaviorNoteController::class, 'updateTeacherBehaviorNote']);
    Route::delete('teacher/behavior-notes/{id}', [BehaviorNoteController::class, 'deleteTeacherBehaviorNote']);
});
