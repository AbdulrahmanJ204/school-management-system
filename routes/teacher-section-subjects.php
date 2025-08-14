<?php

use App\Http\Controllers\TeacherSectionSubjectController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::get('teacher-section-subjects/trashed', [TeacherSectionSubjectController::class, 'trashed']);
    Route::apiResource('teacher-section-subjects', TeacherSectionSubjectController::class);
    Route::patch('teacher-section-subjects/{id}/restore', [TeacherSectionSubjectController::class, 'restore']);
    Route::delete('teacher-section-subjects/{id}/force-delete', [TeacherSectionSubjectController::class, 'forceDelete']);
    Route::get('teacher-section-subjects/teacher/{teacherId}', [TeacherSectionSubjectController::class, 'getByTeacher']);
    Route::get('teacher-section-subjects/section/{sectionId}', [TeacherSectionSubjectController::class, 'getBySection']);
    Route::get('teacher-section-subjects/subject/{subjectId}', [TeacherSectionSubjectController::class, 'getBySubject']);
});
