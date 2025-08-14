<?php

use App\Http\Controllers\ComplaintController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::get('complaints/trashed', [ComplaintController::class, 'trashed']);
    Route::apiResource('complaints', ComplaintController::class);
    Route::patch('complaints/{id}/restore', [ComplaintController::class, 'restore']);
    Route::delete('complaints/{id}/force-delete', [ComplaintController::class, 'forceDelete']);
    Route::get('complaints/user/{userId}', [ComplaintController::class, 'getByUser']);
    Route::post('complaints/{id}/answer', [ComplaintController::class, 'answer']);
}); 