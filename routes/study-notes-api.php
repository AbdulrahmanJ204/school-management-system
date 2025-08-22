<?php

use App\Http\Controllers\StudyNoteController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:api', 'user_type:student'])->group(function () {
    Route::get('student/study-notes', [StudyNoteController::class, 'getStudentStudyNotes']);
});

Route::middleware(['auth:api', 'user_type:teacher'])->group(function () {
    Route::get('teacher/study-notes', [StudyNoteController::class, 'getTeacherStudyNotes']);
    Route::post('teacher/study-notes', [StudyNoteController::class, 'createTeacherStudyNote']);
    Route::put('teacher/study-notes/{id}', [StudyNoteController::class, 'updateTeacherStudyNote']);
    Route::delete('teacher/study-notes/{id}', [StudyNoteController::class, 'deleteTeacherStudyNote']);
});
