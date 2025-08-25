<?php

use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::prefix('students')->group(function () {
    Route::middleware(['auth:api', 'user_type:admin', 'throttle:60,1'])->group(function () {
        Route::get('/', [StudentController::class, 'show']);
        Route::get('/section/{sectionId}', [StudentController::class, 'getBySection']);
    });
    Route::middleware(['auth:api', 'user_type:student', 'throttle:60,1'])->group(function () {
        Route::get('/profile', [StudentController::class, 'getMyProfile']);
    });
});
