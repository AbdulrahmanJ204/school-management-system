<?php

use App\Http\Controllers\NewsController;
use Illuminate\Support\Facades\Route;

Route::prefix('news')
    ->controller(NewsController::class)
    ->middleware('auth:api')
    ->group(function () {
        Route::get('/', 'index');
        Route::get('/{news}', 'show');

        Route::post('/', 'store');
        Route::post('/restore/{news}', 'restore');
        Route::post('/{news}', 'update');

        Route::delete('/delete/{news}', 'delete');
        Route::delete('/{news}', 'destroy');
    });

