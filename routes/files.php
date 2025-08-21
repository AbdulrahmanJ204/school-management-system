<?php

use App\Http\Controllers\FileController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->prefix('files')->controller(FileController::class)->group(function () {
    Route::get('/', 'index')->name('files.index');
    Route::get('/trashed', 'listDeleted')->name('files.trashed');
    Route::get('/download/{file}', 'download')->name('files.download');
    Route::get('/{file}', 'show')->name('files.show');

    Route::post('/', 'store')->name('files.store');
    Route::post('/restore/{file}', 'restore')->name('files.store');
    Route::post('/{file}', 'update')->name('files.update');

    Route::delete('delete/{file}', 'delete')->name('files.delete');
    Route::delete('/{file}', 'destroy')->name('files.destroy');
});


