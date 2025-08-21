<?php

namespace App\Http\Controllers;

use App\Exceptions\PermissionException;
use App\Services\StudentService;
use App\Http\Requests\GetStudentsBySectionSemesterRequest;
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
    public function getBySectionAndSemester(GetStudentsBySectionSemesterRequest $request): JsonResponse
    {
        return $this->studentService->getStudentsBySectionAndSemester(
            $request->section_id,
            $request->semester_id
        );
    }

    /**
     * Get authenticated student's own profile with comprehensive statistics
     * @throws PermissionException
     */
    public function getMyProfile(): JsonResponse
    {
        return $this->studentService->getMyProfile();
    }
}
