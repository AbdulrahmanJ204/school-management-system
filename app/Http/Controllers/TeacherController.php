<?php

namespace App\Http\Controllers;

use App\Exceptions\PermissionException;
use App\Services\TeacherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    protected TeacherService $teacherService;
    public function __construct(TeacherService $teacherService)
    {
        $this->teacherService = $teacherService;
    }

    /**
     * @throws PermissionException
     */
    public function show(): JsonResponse
    {
        return $this->teacherService->listTeachers();
    }

    /**
     * @throws PermissionException
     */
    public function getGradesSectionsSubjects(): JsonResponse
    {
        return $this->teacherService->getTeacherGradesSectionsSubjects();
    }

    /**
     * @throws PermissionException
     */
    public function getStudentsInSectionWithMarks(Request $request, int $sectionId, int $subjectId): JsonResponse
    {
        return $this->teacherService->getStudentsInSectionWithMarks($sectionId, $subjectId);
    }

    /**
     * @throws PermissionException
     */
    public function getProfile(): JsonResponse
    {
        return $this->teacherService->getTeacherProfile();
    }
}
