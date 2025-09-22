<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/reset-password/{token}', function ($token) {
    return 'Reset link: ' . $token;
})->name('password.reset');
