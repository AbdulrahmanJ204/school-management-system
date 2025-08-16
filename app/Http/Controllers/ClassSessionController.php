<?php

namespace App\Http\Controllers;

use App\Exceptions\PermissionException;
use App\Http\Requests\ClassSessionRequest;
use App\Models\ClassSession;
use App\Services\ClassSessionService;
use Illuminate\Http\JsonResponse;

class ClassSessionController extends Controller
{
    protected ClassSessionService $classSessionService;

    public function __construct(ClassSessionService $classSessionService)
    {
        $this->classSessionService = $classSessionService;
    }

    /**
     * Display a listing of the resource.
     * @throws PermissionException
     */
    public function index(): JsonResponse
    {
        return $this->classSessionService->listClassSessions();
    }

    /**
     * Store a newly created resource in storage.
     * @throws PermissionException
     */
    public function store(ClassSessionRequest $request): JsonResponse
    {
        return $this->classSessionService->createClassSession($request);
    }

    /**
     * Display the specified resource.
     * @throws PermissionException
     */
    public function show(ClassSession $classSession): JsonResponse
    {
        return $this->classSessionService->showClassSession($classSession);
    }

    /**
     * Update the specified resource in storage.
     * @throws PermissionException
     */
    public function update(ClassSessionRequest $request, ClassSession $classSession): JsonResponse
    {
        return $this->classSessionService->updateClassSession($request, $classSession);
    }

    /**
     * Remove the specified resource from storage.
     * @throws PermissionException
     */
    public function destroy(ClassSession $classSession): JsonResponse
    {
        return $this->classSessionService->destroyClassSession($classSession);
    }

    /**
     * Cancel a class session.
     * @throws PermissionException
     */
    public function cancel(ClassSession $classSession): JsonResponse
    {
        return $this->classSessionService->cancelClassSession($classSession);
    }

    /**
     * Get class sessions by teacher.
     * @throws PermissionException
     */
    public function getByTeacher($teacherId): JsonResponse
    {
        return $this->classSessionService->getClassSessionsByTeacher($teacherId);
    }

    /**
     * Get class sessions by section.
     * @throws PermissionException
     */
    public function getBySection($sectionId): JsonResponse
    {
        return $this->classSessionService->getClassSessionsBySection($sectionId);
    }

    /**
     * Get class sessions by school day.
     * @throws PermissionException
     */
    public function getBySchoolDay($schoolDayId): JsonResponse
    {
        return $this->classSessionService->getClassSessionsBySchoolDay($schoolDayId);
    }
}
