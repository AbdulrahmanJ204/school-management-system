<?php

namespace App\Http\Controllers;

use App\Http\Requests\TrackClassSessionAttendanceRequest;
use App\Http\Requests\StoreClassSessionAttendanceRequest;
use App\Http\Requests\GetAttendanceHistoryRequest;
use App\Services\TeacherAttendanceTrackingService;
use Illuminate\Http\JsonResponse;

class TeacherAttendanceTrackingController extends Controller
{
    protected TeacherAttendanceTrackingService $teacherAttendanceTrackingService;

    public function __construct(TeacherAttendanceTrackingService $teacherAttendanceTrackingService)
    {
        $this->teacherAttendanceTrackingService = $teacherAttendanceTrackingService;
    }

    /**
     * Track all student attendance in a class session
     * GET /api/teacher/class-sessions/{sessionId}/attendance
     */
    public function trackClassSessionAttendance(int $sessionId, TrackClassSessionAttendanceRequest $request): JsonResponse
    {
        return $this->teacherAttendanceTrackingService->trackClassSessionAttendance($sessionId, $request);
    }

    /**
     * Store student attendance for a class session
     * POST /api/teacher/class-sessions/{sessionId}/attendance
     */
    public function storeClassSessionAttendance(int $sessionId, StoreClassSessionAttendanceRequest $request): JsonResponse
    {
        return $this->teacherAttendanceTrackingService->storeClassSessionAttendance($sessionId, $request);
    }

    /**
     * Get attendance history for teacher's sections/subjects
     * GET /api/teacher/attendance/history
     */
    public function getAttendanceHistory(GetAttendanceHistoryRequest $request): JsonResponse
    {
        return $this->teacherAttendanceTrackingService->getAttendanceHistory($request);
    }

    /**
     * Track individual student attendance
     * GET /api/teacher/students/{studentId}/attendance
     */
    public function trackStudentAttendance(int $studentId): JsonResponse
    {
        return $this->teacherAttendanceTrackingService->trackStudentAttendance($studentId);
    }
}
