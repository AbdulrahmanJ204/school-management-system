<?php

namespace App\Http\Controllers;

use App\Exceptions\PermissionException;
use App\Services\StudentService;
use App\Http\Requests\GetStudentsBySectionRequest;
use Illuminate\Http\JsonResponse;

class StudentController extends Controller
{
    protected StudentService $studentService;
    public function __construct(StudentService $studentService)
    {
        $this->studentService = $studentService;
    }

    /**
     * @throws PermissionException
     */
    public function show(): JsonResponse
    {
        return $this->studentService->listStudents();
    }

    /**
     * @throws PermissionException
     */
    public function getBySection(GetStudentsBySectionRequest $request): JsonResponse
    {
        return $this->studentService->getStudentsBySection($request->sectionId);
    }

    /**
     * Get authenticated student's own profile with comprehensive statistics
     * @throws PermissionException
     */
    public function getMyProfile(): JsonResponse
    {
        return $this->studentService->getMyProfile();
    }

    /**
     * Get all years and semesters for student
     * 
     * @return JsonResponse
     */
    public function getYearsAndSemesters(): JsonResponse
    {
        return $this->studentService->getYearsAndSemesters();
    }
}
