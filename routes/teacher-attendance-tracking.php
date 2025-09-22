<?php

use App\Http\Controllers\TeacherAttendanceTrackingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:api', 'user_type:teacher'])
    ->prefix('teacher')
    ->controller(TeacherAttendanceTrackingController::class)
    ->group(function () {
        
        // Track all student attendance in class session
        Route::get('class-sessions/{sessionId}/attendance', 'trackClassSessionAttendance')
            ->name('teacher.class-sessions.attendance.track');
        
        // Store student attendance for class session
        Route::post('class-sessions/{sessionId}/attendance', 'storeClassSessionAttendance')
            ->name('teacher.class-sessions.attendance.store');
        
        // Get attendance history for teacher's sections/subjects
        Route::get('attendance/history', 'getAttendanceHistory')
            ->name('teacher.attendance.history');
        
        // Track individual student attendance
        Route::get('students/{studentId}/attendance', 'trackStudentAttendance')
            ->name('teacher.students.attendance.track');
            
    });

