<?php

use App\Http\Controllers\FileController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->prefix('admin/files')->controller(FileController::class)->group(function () {
    Route::get('/', 'index')->name('files');
    Route::post('/store', 'store')->name('files.store');
    Route::post('/{file}', 'update')->name('files.store');
    Route::delete('/{file}', 'destroy')->name('files.store');
})->middleware(['role:admin']);

Route::middleware('auth:api')->prefix('teacher/files')->controller(FileController::class)->group(function () {
    Route::get('/', 'index')->name('files');
    Route::post('/store', 'store')->name('files.store');
})->middleware(['role:teacher']);

Route::middleware('auth:api')->prefix('student/files')->controller(FileController::class)->group(function () {
    Route::get('/', 'index')->name('files');

})->middleware(['role:student']);
