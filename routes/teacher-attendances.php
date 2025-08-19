<?php

use App\Http\Controllers\TeacherAttendanceController;
use Illuminate\Support\Facades\Route;

Route::prefix('teacher-attendances')
    ->controller(TeacherAttendanceController::class)
    ->middleware('auth:api')
    ->group(function () {
        Route::get('/', 'index');
        Route::get('/{teacherAttendance}', 'show');

        Route::post('/store', 'store');
        Route::post('/{teacherAttendance}', 'update');

        Route::delete('/{teacherAttendance}', 'destroy');
    });
