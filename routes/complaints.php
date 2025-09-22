<?php

use App\Http\Controllers\ComplaintController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::get('complaints/trashed', [ComplaintController::class, 'trashed']);
    
    // CRUD routes for complaints
    Route::get('complaints', [ComplaintController::class, 'index']);
    Route::post('complaints', [ComplaintController::class, 'store']);
    Route::get('complaints/{complaint}', [ComplaintController::class, 'show']);
    Route::put('complaints/{complaint}', [ComplaintController::class, 'update']);
    Route::delete('complaints/{complaint}', [ComplaintController::class, 'destroy']);
    
    Route::patch('complaints/{id}/restore', [ComplaintController::class, 'restore']);
    Route::delete('complaints/{id}/force-delete', [ComplaintController::class, 'forceDelete']);
    Route::get('complaints/user/{userId}', [ComplaintController::class, 'getByUser']);
    Route::post('complaints/{id}/answer', [ComplaintController::class, 'answer']);
}); 