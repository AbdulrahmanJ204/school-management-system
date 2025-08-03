<?php

use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::get('messages/trashed', [MessageController::class, 'trashed']);
    Route::apiResource('messages', MessageController::class);
    Route::patch('messages/{id}/restore', [MessageController::class, 'restore']);
    Route::delete('messages/{id}/force-delete', [MessageController::class, 'forceDelete']);
    Route::get('messages/user/{userId}', [MessageController::class, 'getByUser']);
});
