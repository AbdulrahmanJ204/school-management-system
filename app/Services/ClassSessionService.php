<?php

namespace App\Services;

use App\Enums\PermissionEnum;
use App\Exceptions\PermissionException;
use App\Helpers\ResponseHelper;
use App\Http\Requests\ClassSessionRequest;
use App\Http\Resources\ClassSessionResource;
use App\Models\ClassSession;
use App\Models\Schedule;
use App\Models\SchoolDay;
use App\Traits\HasPermissionChecks;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ClassSessionService
{
    use HasPermissionChecks;

    /**
     * Get list of all class sessions.
     * @throws PermissionException
     */
    public function listClassSessions(): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_CLASS_SESSIONS);

        $classSessions = ClassSession::with([
            'schedule',
            'schoolDay',
            'teacher.user',
            'subject',
            'section.grade',
            'classPeriod',
            'createdBy'
        ])->orderBy('created_at', 'desc')->get();

        return ResponseHelper::jsonResponse(
            ClassSessionResource::collection($classSessions)
        );
    }

    /**
     * Create a new class session.
     * @throws PermissionException
     */
    public function createClassSession(ClassSessionRequest $request): JsonResponse
    {
        $this->checkPermission(PermissionEnum::CREATE_CLASS_SESSION);

        $credentials = $request->validated();
        $credentials['created_by'] = auth()->id();

        // Check if session already exists for this schedule and school day
        $existingSession = ClassSession::where('schedule_id', $credentials['schedule_id'])
            ->where('school_day_id', $credentials['school_day_id'])
            ->first();

        if ($existingSession) {
            return ResponseHelper::jsonResponse(
                null,
                __('messages.class_session.already_exists'),
                ResponseAlias::HTTP_BAD_REQUEST,
                false
            );
        }

        $classSession = ClassSession::create($credentials);
        $classSession->load([
            'schedule',
            'schoolDay',
            'teacher.user',
            'subject',
            'section.grade',
            'classPeriod',
            'createdBy'
        ]);

        return ResponseHelper::jsonResponse(
            new ClassSessionResource($classSession),
            __('messages.class_session.created'),
            ResponseAlias::HTTP_CREATED,
            true
        );
    }

    /**
     * Show a specific class session.
     * @throws PermissionException
     */
    public function showClassSession(ClassSession $classSession): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_CLASS_SESSION);

        $classSession->load([
            'schedule',
            'schoolDay',
            'teacher.user',
            'subject',
            'section.grade',
            'classPeriod',
            'createdBy',
            'studentAttendances.student.user',
            'assignments',
            'studyNotes'
        ]);

        return ResponseHelper::jsonResponse(
            new ClassSessionResource($classSession)
        );
    }

    /**
     * Update a class session.
     * @throws PermissionException
     */
    public function updateClassSession(ClassSessionRequest $request, ClassSession $classSession): JsonResponse
    {
        $this->checkPermission(PermissionEnum::UPDATE_CLASS_SESSION);

        $credentials = $request->validated();

        // Check if session already exists for this schedule and school day (excluding current session)
        $existingSession = ClassSession::where('schedule_id', $credentials['schedule_id'])
            ->where('school_day_id', $credentials['school_day_id'])
            ->where('id', '!=', $classSession->id)
            ->first();

        if ($existingSession) {
            return ResponseHelper::jsonResponse(
                null,
                __('messages.class_session.already_exists'),
                ResponseAlias::HTTP_BAD_REQUEST,
                false
            );
        }

        $classSession->update($credentials);
        $classSession->load([
            'schedule',
            'schoolDay',
            'teacher.user',
            'subject',
            'section.grade',
            'classPeriod',
            'createdBy'
        ]);

        return ResponseHelper::jsonResponse(
            new ClassSessionResource($classSession),
            __('messages.class_session.updated')
        );
    }

    /**
     * Delete a class session.
     * @throws PermissionException
     */
    public function destroyClassSession(ClassSession $classSession): JsonResponse
    {
        $this->checkPermission(PermissionEnum::DELETE_CLASS_SESSION);

        if ($classSession->studentAttendances()->exists()) {
            return ResponseHelper::jsonResponse(
                null,
                __('messages.class_session.has_attendances'),
                ResponseAlias::HTTP_BAD_REQUEST,
                false
            );
        }

        if ($classSession->assignments()->exists()) {
            return ResponseHelper::jsonResponse(
                null,
                __('messages.class_session.has_assignments'),
                ResponseAlias::HTTP_BAD_REQUEST,
                false
            );
        }

        $classSession->delete();

        return ResponseHelper::jsonResponse(
            null,
            __('messages.class_session.deleted')
        );
    }



    /**
     * Cancel a class session.
     * @throws PermissionException
     */
    public function cancelClassSession(ClassSession $classSession): JsonResponse
    {
        $this->checkPermission(PermissionEnum::UPDATE_CLASS_SESSION);

        if ($classSession->status === 'completed') {
            return ResponseHelper::jsonResponse(
                null,
                __('messages.class_session.cannot_cancel'),
                ResponseAlias::HTTP_BAD_REQUEST,
                false
            );
        }

        $classSession->update(['status' => 'cancelled']);

        return ResponseHelper::jsonResponse(
            new ClassSessionResource($classSession),
            __('messages.class_session.cancelled')
        );
    }

    /**
     * Get class sessions by teacher.
     * @throws PermissionException
     */
    public function getClassSessionsByTeacher($teacherId): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_CLASS_SESSIONS);

        $classSessions = ClassSession::where('teacher_id', $teacherId)->with([
            'schedule',
            'schoolDay',
            'teacher.user',
            'subject',
            'section.grade',
            'classPeriod',
            'createdBy'
        ])->orderBy('created_at', 'desc')->get();

        return ResponseHelper::jsonResponse(
            ClassSessionResource::collection($classSessions)
        );
    }

    /**
     * Get class sessions by section.
     * @throws PermissionException
     */
    public function getClassSessionsBySection($sectionId): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_CLASS_SESSIONS);

        $classSessions = ClassSession::where('section_id', $sectionId)->with([
            'schedule',
            'schoolDay',
            'teacher.user',
            'subject',
            'section.grade',
            'classPeriod',
            'createdBy'
        ])->orderBy('created_at', 'desc')->get();

        return ResponseHelper::jsonResponse(
            ClassSessionResource::collection($classSessions)
        );
    }

    /**
     * Get class sessions by school day.
     * @throws PermissionException
     */
    public function getClassSessionsBySchoolDay($schoolDayId): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_CLASS_SESSIONS);

        $classSessions = ClassSession::where('school_day_id', $schoolDayId)->with([
            'schedule',
            'schoolDay',
            'teacher.user',
            'subject',
            'section.grade',
            'classPeriod',
            'createdBy'
        ])->orderBy('created_at', 'desc')->get();

        return ResponseHelper::jsonResponse(
            ClassSessionResource::collection($classSessions)
        );
    }
}
