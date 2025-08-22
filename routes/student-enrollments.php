<?php

use App\Http\Controllers\StudentEnrollmentController;
use Illuminate\Support\Facades\Route;

Route::prefix('student-enrollments')->group(function () {
    Route::middleware(['auth:api', 'user_type:admin', 'throttle:5,1'])->group(function () {
        Route::get('/trashed', [StudentEnrollmentController::class, 'trashed']);
        Route::apiResource('/', StudentEnrollmentController::class);
        Route::patch('/{id}/restore', [StudentEnrollmentController::class, 'restore']);
        Route::delete('/{id}/force-delete', [StudentEnrollmentController::class, 'forceDelete']);
    });
});
