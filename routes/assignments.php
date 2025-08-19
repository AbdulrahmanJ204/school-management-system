<?php

use App\Http\Controllers\AssignmentController;
use Illuminate\Support\Facades\Route;

Route::prefix('assignments')
    ->controller(AssignmentController::class)
    ->middleware('auth:api')
    ->group(function () {
        Route::get('/', 'index');
        Route::get('/{assignment}', 'show');

        Route::post('/store', 'store');
        Route::post('/restore/{assignment}', 'restore');
        Route::post('/{assignment}', 'update');

        Route::delete('/delete/{assignment}', 'delete');
        Route::delete('/{assignment}', 'destroy');
    });
