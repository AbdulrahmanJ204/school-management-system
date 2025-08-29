<?php

use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::get('messages/trashed', [MessageController::class, 'trashed']);
    
    // CRUD routes for messages
    Route::get('messages', [MessageController::class, 'index']);
    Route::post('messages', [MessageController::class, 'store']);
    Route::get('messages/{message}', [MessageController::class, 'show']);
    Route::put('messages/{message}', [MessageController::class, 'update']);
    Route::delete('messages/{message}', [MessageController::class, 'destroy']);
    
    Route::patch('messages/{id}/restore', [MessageController::class, 'restore']);
    Route::delete('messages/{id}/force-delete', [MessageController::class, 'forceDelete']);
    Route::get('messages/user/{userId}', [MessageController::class, 'getByUser']);
});
