<?php

use App\Http\Controllers\NewsController;
use Illuminate\Support\Facades\Route;
Route::prefix('student/news')->group(function () {
    Route::get('/', [NewsController::class, 'index'])->name('news.index');
})->middleware('auth:api')->middleware(['role:student']);
Route::apiResource('admin/news', NewsController::class)->names('admin.news');
