<?php

use App\Http\Controllers\NewsController;
use Illuminate\Support\Facades\Route;
Route::prefix('student/news')->group(function () {
    Route::get('/', [NewsController::class, 'index'])->name('news.index');
})->middleware(['auth:api','role:student']);
Route::prefix('admin/news')
    ->controller(NewsController::class)
    ->middleware(['auth:api'])
    ->group(function () {
        Route::get('/', 'index');
        Route::get('/{id}', 'show');
        Route::post('/store', 'store');
    });
