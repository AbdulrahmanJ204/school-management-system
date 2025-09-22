<?php

use App\Http\Controllers\ExamController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::get('exams/trashed', [ExamController::class, 'trashed']);
    
    // CRUD routes for exams
    Route::get('exams', [ExamController::class, 'index']);
    Route::post('exams', [ExamController::class, 'store']);
    Route::get('exams/{exam}', [ExamController::class, 'show']);
    Route::put('exams/{exam}', [ExamController::class, 'update']);
    Route::delete('exams/{exam}', [ExamController::class, 'destroy']);
    
    Route::patch('exams/{id}/restore', [ExamController::class, 'restore']);
    Route::delete('exams/{id}/force-delete', [ExamController::class, 'forceDelete']);
});
