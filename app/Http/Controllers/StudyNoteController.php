<?php

namespace App\Http\Controllers;

use App\Exceptions\PermissionException;
use App\Http\Requests\StudyNote\ListStudyNoteRequest;
use App\Http\Requests\StudyNote\StoreStudyNoteRequest;
use App\Http\Requests\StudyNote\UpdateStudyNoteRequest;

use App\Http\Requests\CombinedNotesRequest;
use App\Services\StudyNoteService;
use App\Services\CombinedNotesService;
use Illuminate\Http\JsonResponse;

class StudyNoteController extends Controller
{
    protected StudyNoteService $studyNoteService;
    protected CombinedNotesService $combinedNotesService;

    public function __construct(StudyNoteService $studyNoteService, CombinedNotesService $combinedNotesService)
    {
        $this->studyNoteService = $studyNoteService;
        $this->combinedNotesService = $combinedNotesService;
    }

    /**
     * @throws PermissionException
     */
    public function index(ListStudyNoteRequest $request): JsonResponse
    {
        return $this->studyNoteService->listStudyNotes($request);
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
    public function store(StoreStudyNoteRequest $request): JsonResponse
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
    public function update(UpdateStudyNoteRequest $request, $id): JsonResponse
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
    public function getCombinedNotes(CombinedNotesRequest $request): JsonResponse
    {
        return $this->combinedNotesService->getCombinedNotes($request);
    }

    /**
     * Get study notes for student (no pagination, with filters)
     * @throws PermissionException
     */
    public function getStudentStudyNotes(ListStudyNoteRequest $request): JsonResponse
    {
        return $this->studyNoteService->getStudentStudyNotes($request);
    }

    /**
     * Get study notes for teacher (no pagination, with filters)
     * @throws PermissionException
     */
    public function getTeacherStudyNotes(ListStudyNoteRequest $request): JsonResponse
    {
        return $this->studyNoteService->getTeacherStudyNotes($request);
    }

    /**
     * Create study note by teacher
     * @throws PermissionException
     */
    public function createTeacherStudyNote(StoreStudyNoteRequest $request): JsonResponse
    {
        return $this->studyNoteService->createTeacherStudyNote($request);
    }

    /**
     * Update study note by teacher
     * @throws PermissionException
     */
    public function updateTeacherStudyNote(UpdateStudyNoteRequest $request, $id): JsonResponse
    {
        return $this->studyNoteService->updateTeacherStudyNote($request, $id);
    }

    /**
     * Delete study note by teacher
     * @throws PermissionException
     */
    public function deleteTeacherStudyNote($id): JsonResponse
    {
        return $this->studyNoteService->deleteTeacherStudyNote($id);
    }
}
