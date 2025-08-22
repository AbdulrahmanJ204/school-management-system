<?php

namespace App\Http\Controllers;

use App\Exceptions\PermissionException;
use App\Http\Requests\BehaviorNoteRequest;
use App\Http\Requests\BehaviorNote\ListBehaviorNoteRequest;
use App\Http\Requests\BehaviorNote\StoreBehaviorNoteRequest;
use App\Http\Requests\BehaviorNote\UpdateBehaviorNoteRequest;
use App\Services\BehaviorNoteService;
use Illuminate\Http\JsonResponse;

class BehaviorNoteController extends Controller
{
    protected BehaviorNoteService $behaviorNoteService;

    public function __construct(BehaviorNoteService $behaviorNoteService)
    {
        $this->behaviorNoteService = $behaviorNoteService;
    }

    /**
     * @throws PermissionException
     */
    public function index(): JsonResponse
    {
        return $this->behaviorNoteService->listBehaviorNotes();
    }

    /**
     * @throws PermissionException
     */
    public function trashed(): JsonResponse
    {
        return $this->behaviorNoteService->listTrashedBehaviorNotes();
    }

    /**
     * @throws PermissionException
     */
    public function store(BehaviorNoteRequest $request): JsonResponse
    {
        return $this->behaviorNoteService->createBehaviorNote($request);
    }

    /**
     * @throws PermissionException
     */
    public function show($id): JsonResponse
    {
        return $this->behaviorNoteService->showBehaviorNote($id);
    }

    /**
     * @throws PermissionException
     */
    public function update(BehaviorNoteRequest $request, $id): JsonResponse
    {
        return $this->behaviorNoteService->updateBehaviorNote($request, $id);
    }

    /**
     * @throws PermissionException
     */
    public function destroy($id): JsonResponse
    {
        return $this->behaviorNoteService->deleteBehaviorNote($id);
    }

    /**
     * @throws PermissionException
     */
    public function restore($id): JsonResponse
    {
        return $this->behaviorNoteService->restoreBehaviorNote($id);
    }

    /**
     * @throws PermissionException
     */
    public function forceDelete($id): JsonResponse
    {
        return $this->behaviorNoteService->forceDeleteBehaviorNote($id);
    }

    /**
     * @throws PermissionException
     */
    public function getByStudent($studentId): JsonResponse
    {
        return $this->behaviorNoteService->getByStudent($studentId);
    }

    /**
     * @throws PermissionException
     */
    public function getBySchoolDay($schoolDayId): JsonResponse
    {
        return $this->behaviorNoteService->getBySchoolDay($schoolDayId);
    }

    /**
     * Get behavior notes for student (no pagination, with filters)
     * @throws PermissionException
     */
    public function getStudentBehaviorNotes(ListBehaviorNoteRequest $request): JsonResponse
    {
        return $this->behaviorNoteService->getStudentBehaviorNotes($request);
    }

    /**
     * Get behavior notes for teacher (no pagination, with filters)
     * @throws PermissionException
     */
    public function getTeacherBehaviorNotes(ListBehaviorNoteRequest $request): JsonResponse
    {
        return $this->behaviorNoteService->getTeacherBehaviorNotes($request);
    }

    /**
     * Create behavior note by teacher
     * @throws PermissionException
     */
    public function createTeacherBehaviorNote(StoreBehaviorNoteRequest $request): JsonResponse
    {
        return $this->behaviorNoteService->createTeacherBehaviorNote($request);
    }

    /**
     * Update behavior note by teacher
     * @throws PermissionException
     */
    public function updateTeacherBehaviorNote(UpdateBehaviorNoteRequest $request, $id): JsonResponse
    {
        return $this->behaviorNoteService->updateTeacherBehaviorNote($request, $id);
    }

    /**
     * Delete behavior note by teacher
     * @throws PermissionException
     */
    public function deleteTeacherBehaviorNote($id): JsonResponse
    {
        return $this->behaviorNoteService->deleteTeacherBehaviorNote($id);
    }
}
