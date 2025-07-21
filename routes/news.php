<?php

use App\Http\Controllers\NewsController;
use Illuminate\Support\Facades\Route;

Route::prefix('student/news')->middleware(['auth:api'])->group(function () {
    Route::get('/', [NewsController::class, 'index'])->name('news.index');
})->middleware(['role:student']);
Route::prefix('admin/news')
    ->controller(NewsController::class)
    ->middleware(['auth:api' , 'role:admin'])
    ->group(function () {
        Route::get('/', 'index');
        Route::post('/store', 'store');
        Route::get('/{news}', 'show');
        Route::delete('/{news}', 'destroy');
        Route::post('/{news}', 'update');
    });
