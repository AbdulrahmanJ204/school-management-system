<?php

use App\Http\Controllers\NewsController;
use Illuminate\Support\Facades\Route;

Route::prefix('news')
    ->controller(NewsController::class)
    ->middleware('auth:api')
    ->group(function () {
        Route::get('/', 'index');
        Route::post('/store', 'store');
        Route::post('/restore/{news}', 'restore');
        Route::get('/{news}', 'show');
        Route::delete('/{news}', 'destroy');
        Route::post('/{news}', 'update');
    });

