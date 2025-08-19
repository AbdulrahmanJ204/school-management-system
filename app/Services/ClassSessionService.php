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
    public function listClassSessions(): void
    {
        $this->checkPermission(PermissionEnum::VIEW_CLASS_SESSIONS);
        //
    }

    /**
     * Create a new class session.
     * @throws PermissionException
     */
    public function createClassSession(ClassSessionRequest $request): void
    {
        $this->checkPermission(PermissionEnum::CREATE_CLASS_SESSION);
        //
    }

    /**
     * Show a specific class session.
     * @throws PermissionException
     */
    public function showClassSession(ClassSession $classSession): void
    {
        $this->checkPermission(PermissionEnum::VIEW_CLASS_SESSION);
        //
    }

    /**
     * Update a class session.
     * @throws PermissionException
     */
    public function updateClassSession(ClassSessionRequest $request, ClassSession $classSession): void
    {
        $this->checkPermission(PermissionEnum::UPDATE_CLASS_SESSION);
        //
    }

    /**
     * Delete a class session.
     * @throws PermissionException
     */
    public function destroyClassSession(ClassSession $classSession): void
    {
        $this->checkPermission(PermissionEnum::DELETE_CLASS_SESSION);
        //
    }



    /**
     * Cancel a class session.
     * @throws PermissionException
     */
    public function cancelClassSession(ClassSession $classSession): void
    {
        $this->checkPermission(PermissionEnum::UPDATE_CLASS_SESSION);
        //
    }

    /**
     * Get class sessions by teacher.
     * @throws PermissionException
     */
    public function getClassSessionsByTeacher($teacherId): void
    {
        $this->checkPermission(PermissionEnum::VIEW_CLASS_SESSIONS);
        //
    }

    /**
     * Get class sessions by section.
     * @throws PermissionException
     */
    public function getClassSessionsBySection($sectionId): void
    {
        $this->checkPermission(PermissionEnum::VIEW_CLASS_SESSIONS);
        //
    }

    /**
     * Get class sessions by school day.
     * @throws PermissionException
     */
    public function getClassSessionsBySchoolDay($schoolDayId): void
    {
        $this->checkPermission(PermissionEnum::VIEW_CLASS_SESSIONS);
        //
    }
}
