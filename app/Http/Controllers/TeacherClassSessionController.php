<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\TeacherClassSessionRequest;
use App\Services\TeacherClassSessionService;
use Illuminate\Http\JsonResponse;

class TeacherClassSessionController extends Controller
{
    protected $teacherClassSessionService;

    public function __construct(TeacherClassSessionService $teacherClassSessionService)
    {
        $this->teacherClassSessionService = $teacherClassSessionService;
    }

    /**
     * Get past week class sessions for a teacher
     */
    public function getPastWeekSessions(TeacherClassSessionRequest $request): JsonResponse
    {
        $validated = $request->validated();
        
        $sessions = $this->teacherClassSessionService->getPastWeekSessions(
            $validated['subject_id'],
            $validated['section_id']
        );

        // If no sessions found, return appropriate message
        if (empty($sessions)) {
            return ResponseHelper::jsonResponse(
                ['sessions' => []],
                __('messages.teacher_class_session.no_sessions_found'),
                200
            );
        }

        return ResponseHelper::jsonResponse(
            ['sessions' => $sessions],
            __('messages.teacher_class_session.past_week_success'),
            200
        );
    }

    /**
     * Get upcoming two weeks class sessions for a teacher
     */
    public function getUpcomingSessions(TeacherClassSessionRequest $request): JsonResponse
    {
        $validated = $request->validated();
        
        $sessions = $this->teacherClassSessionService->getUpcomingSessions(
            $validated['subject_id'],
            $validated['section_id']
        );

        // If no sessions found, return appropriate message
        if (empty($sessions)) {
            return ResponseHelper::jsonResponse(
                ['sessions' => []],
                __('messages.teacher_class_session.no_sessions_found'),
                200
            );
        }

        return ResponseHelper::jsonResponse(
            ['sessions' => $sessions],
            __('messages.teacher_class_session.upcoming_success'),
            200
        );
    }
}
