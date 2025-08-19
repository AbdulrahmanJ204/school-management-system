<?php

use App\Http\Controllers\StudentAttendanceController;
use Illuminate\Support\Facades\Route;

Route::prefix('student-attendances')
    ->controller(StudentAttendanceController::class)
    ->middleware('auth:api')
    ->group(function () {
        Route::get('/', 'index');
        Route::get('/{studentAttendance}', 'show');

        Route::post('/store', 'store');
        Route::post('/{studentAttendance}', 'update');

        Route::delete('/{studentAttendance}', 'destroy');
    });
