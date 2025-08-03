<?php

namespace App\Http\Controllers;

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

    public function index(): JsonResponse
    {
        return $this->teacherSectionSubjectService->listTeacherSectionSubjects();
    }

    public function trashed(): JsonResponse
    {
        return $this->teacherSectionSubjectService->listTrashedTeacherSectionSubjects();
    }

    public function store(TeacherSectionSubjectRequest $request): JsonResponse
    {
        return $this->teacherSectionSubjectService->createTeacherSectionSubject($request);
    }

    public function show($id): JsonResponse
    {
        return $this->teacherSectionSubjectService->showTeacherSectionSubject($id);
    }

    public function update(TeacherSectionSubjectRequest $request, $id): JsonResponse
    {
        return $this->teacherSectionSubjectService->updateTeacherSectionSubject($request, $id);
    }

    public function destroy($id): JsonResponse
    {
        return $this->teacherSectionSubjectService->deleteTeacherSectionSubject($id);
    }

    public function restore($id): JsonResponse
    {
        return $this->teacherSectionSubjectService->restoreTeacherSectionSubject($id);
    }

    public function forceDelete($id): JsonResponse
    {
        return $this->teacherSectionSubjectService->forceDeleteTeacherSectionSubject($id);
    }

    public function getByTeacher($teacherId): JsonResponse
    {
        return $this->teacherSectionSubjectService->getByTeacher($teacherId);
    }

    public function getBySection($sectionId): JsonResponse
    {
        return $this->teacherSectionSubjectService->getBySection($sectionId);
    }

    public function getBySubject($subjectId): JsonResponse
    {
        return $this->teacherSectionSubjectService->getBySubject($subjectId);
    }
}
