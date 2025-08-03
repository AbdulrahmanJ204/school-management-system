<?php

use App\Http\Controllers\StudentEnrollmentController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::get('student-enrollments/trashed', [StudentEnrollmentController::class, 'trashed']);
    Route::apiResource('student-enrollments', StudentEnrollmentController::class);
    Route::patch('student-enrollments/{id}/restore', [StudentEnrollmentController::class, 'restore']);
    Route::delete('student-enrollments/{id}/force-delete', [StudentEnrollmentController::class, 'forceDelete']);
    Route::get('student-enrollments/student/{studentId}', [StudentEnrollmentController::class, 'getByStudent']);
    Route::get('student-enrollments/section/{sectionId}', [StudentEnrollmentController::class, 'getBySection']);
    Route::get('student-enrollments/semester/{semesterId}', [StudentEnrollmentController::class, 'getBySemester']);
});
