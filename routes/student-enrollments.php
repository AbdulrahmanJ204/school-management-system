<?php

use App\Http\Controllers\StudentEnrollmentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:api', 'user_type:admin', 'throttle:60,1'])->group(function () {
    Route::prefix('student-enrollments')->group(function () {
        Route::get('/trashed', [StudentEnrollmentController::class, 'trashed']);
        Route::patch('/{id}/restore', [StudentEnrollmentController::class, 'restore']);
        Route::delete('/{id}/force-delete', [StudentEnrollmentController::class, 'forceDelete']);
    });
    
    // CRUD routes for student-enrollments
    Route::get('student-enrollments', [StudentEnrollmentController::class, 'index']);
    Route::post('student-enrollments', [StudentEnrollmentController::class, 'store']);
    Route::get('student-enrollments/{student_enrollment}', [StudentEnrollmentController::class, 'show']);
    Route::put('student-enrollments/{student_enrollment}', [StudentEnrollmentController::class, 'update']);
    Route::delete('student-enrollments/{student_enrollment}', [StudentEnrollmentController::class, 'destroy']);
});
