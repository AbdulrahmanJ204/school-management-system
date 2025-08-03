<?php

namespace App\Http\Controllers;

use App\Exceptions\PermissionException;
use App\Http\Requests\StudyNoteRequest;
use App\Services\StudyNoteService;
use Illuminate\Http\JsonResponse;

class StudyNoteController extends Controller
{
    protected StudyNoteService $studyNoteService;

    public function __construct(StudyNoteService $studyNoteService)
    {
        $this->studyNoteService = $studyNoteService;
    }

    /**
     * @throws PermissionException
     */
    public function index(): JsonResponse
    {
        return $this->studyNoteService->listStudyNotes();
    }

    /**
     * @throws PermissionException
     */
    public function trashed(): JsonResponse
    {
        return $this->studyNoteService->listTrashedStudyNotes();
    }

    /**
     * @throws PermissionException
     */
    public function store(StudyNoteRequest $request): JsonResponse
    {
        return $this->studyNoteService->createStudyNote($request);
    }

    /**
     * @throws PermissionException
     */
    public function show($id): JsonResponse
    {
        return $this->studyNoteService->showStudyNote($id);
    }

    /**
     * @throws PermissionException
     */
    public function update(StudyNoteRequest $request, $id): JsonResponse
    {
        return $this->studyNoteService->updateStudyNote($request, $id);
    }

    /**
     * @throws PermissionException
     */
    public function destroy($id): JsonResponse
    {
        return $this->studyNoteService->deleteStudyNote($id);
    }

    /**
     * @throws PermissionException
     */
    public function restore($id): JsonResponse
    {
        return $this->studyNoteService->restoreStudyNote($id);
    }

    /**
     * @throws PermissionException
     */
    public function forceDelete($id): JsonResponse
    {
        return $this->studyNoteService->forceDeleteStudyNote($id);
    }

    /**
     * @throws PermissionException
     */
    public function getByStudent($studentId): JsonResponse
    {
        return $this->studyNoteService->getByStudent($studentId);
    }

    /**
     * @throws PermissionException
     */
    public function getBySchoolDay($schoolDayId): JsonResponse
    {
        return $this->studyNoteService->getBySchoolDay($schoolDayId);
    }

    /**
     * @throws PermissionException
     */
    public function getBySubject($subjectId): JsonResponse
    {
        return $this->studyNoteService->getBySubject($subjectId);
    }
}
