<?php

namespace App\Http\Controllers;

use App\Exceptions\PermissionException;
use App\Http\Requests\TeacherSectionSubjectRequest;
use App\Services\TeacherSectionSubjectService;
use Illuminate\Http\JsonResponse;

class TeacherSectionSubjectController extends Controller
{
    protected TeacherSectionSubjectService $teacherSectionSubjectService;

    public function __construct(TeacherSectionSubjectService $teacherSectionSubjectService)
    {
        $this->teacherSectionSubjectService = $teacherSectionSubjectService;
    }

    /**
     * @throws PermissionException
     */
    public function index(): JsonResponse
    {
        return $this->teacherSectionSubjectService->listTeacherSectionSubjects();
    }

    /**
     * @throws PermissionException
     */
    public function trashed(): JsonResponse
    {
        return $this->teacherSectionSubjectService->listTrashedTeacherSectionSubjects();
    }

    /**
     * @throws PermissionException
     */
    public function store(TeacherSectionSubjectRequest $request): JsonResponse
    {
        return $this->teacherSectionSubjectService->createTeacherSectionSubject($request);
    }

    /**
     * @throws PermissionException
     */
    public function show($id): JsonResponse
    {
        return $this->teacherSectionSubjectService->showTeacherSectionSubject($id);
    }

    /**
     * @throws PermissionException
     */
    public function update(TeacherSectionSubjectRequest $request, $id): JsonResponse
    {
        return $this->teacherSectionSubjectService->updateTeacherSectionSubject($request, $id);
    }

    /**
     * @throws PermissionException
     */
    public function destroy($id): JsonResponse
    {
        return $this->teacherSectionSubjectService->deleteTeacherSectionSubject($id);
    }

    /**
     * @throws PermissionException
     */
    public function restore($id): JsonResponse
    {
        return $this->teacherSectionSubjectService->restoreTeacherSectionSubject($id);
    }

    /**
     * @throws PermissionException
     */
    public function forceDelete($id): JsonResponse
    {
        return $this->teacherSectionSubjectService->forceDeleteTeacherSectionSubject($id);
    }

    /**
     * @throws PermissionException
     */
    public function getByTeacher($teacherId): JsonResponse
    {
        return $this->teacherSectionSubjectService->getByTeacher($teacherId);
    }

    /**
     * @throws PermissionException
     */
    public function getBySection($sectionId): JsonResponse
    {
        return $this->teacherSectionSubjectService->getBySection($sectionId);
    }

    /**
     * @throws PermissionException
     */
    public function getBySubject($subjectId): JsonResponse
    {
        return $this->teacherSectionSubjectService->getBySubject($subjectId);
    }
}
