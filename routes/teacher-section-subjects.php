<?php

use App\Http\Controllers\TeacherSectionSubjectController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::get('teacher-section-subjects/trashed', [TeacherSectionSubjectController::class, 'trashed']);
    
    // CRUD routes for teacher-section-subjects
    Route::get('teacher-section-subjects', [TeacherSectionSubjectController::class, 'index']);
    Route::post('teacher-section-subjects', [TeacherSectionSubjectController::class, 'store']);
    Route::get('teacher-section-subjects/{teacher_section_subject}', [TeacherSectionSubjectController::class, 'show']);
    Route::put('teacher-section-subjects/{teacher_section_subject}', [TeacherSectionSubjectController::class, 'update']);
    Route::delete('teacher-section-subjects/{teacher_section_subject}', [TeacherSectionSubjectController::class, 'destroy']);
    
    Route::patch('teacher-section-subjects/{id}/restore', [TeacherSectionSubjectController::class, 'restore']);
    Route::delete('teacher-section-subjects/{id}/force-delete', [TeacherSectionSubjectController::class, 'forceDelete']);
    Route::get('teacher-section-subjects/teacher/{teacherId}', [TeacherSectionSubjectController::class, 'getByTeacher']);
    Route::get('teacher-section-subjects/section/{sectionId}', [TeacherSectionSubjectController::class, 'getBySection']);
    Route::get('teacher-section-subjects/subject/{subjectId}', [TeacherSectionSubjectController::class, 'getBySubject']);
});
