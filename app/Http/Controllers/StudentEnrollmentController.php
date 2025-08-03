<?php

namespace App\Http\Controllers;

use App\Exceptions\PermissionException;
use App\Http\Requests\StudentEnrollmentRequest;
use App\Models\StudentEnrollment;
use App\Services\StudentEnrollmentService;
use Illuminate\Http\JsonResponse;

class StudentEnrollmentController extends Controller
{
    protected StudentEnrollmentService $studentEnrollmentService;

    public function __construct(StudentEnrollmentService $studentEnrollmentService)
    {
        $this->studentEnrollmentService = $studentEnrollmentService;
    }

    /**
     * Display a listing of the resource.
     * @throws PermissionException
     */
    public function index(): JsonResponse
    {
        return $this->studentEnrollmentService->listStudentEnrollments();
    }

    /**
     * Store a newly created resource in storage.
     * @throws PermissionException
     */
    public function store(StudentEnrollmentRequest $request): JsonResponse
    {
        return $this->studentEnrollmentService->createStudentEnrollment($request);
    }

    /**
     * Display the specified resource.
     * @throws PermissionException
     */
    public function show(StudentEnrollment $studentEnrollment): JsonResponse
    {
        return $this->studentEnrollmentService->showStudentEnrollment($studentEnrollment);
    }

    /**
     * Update the specified resource in storage.
     * @throws PermissionException
     */
    public function update(StudentEnrollmentRequest $request, StudentEnrollment $studentEnrollment): JsonResponse
    {
        return $this->studentEnrollmentService->updateStudentEnrollment($request, $studentEnrollment);
    }

    /**
     * Remove the specified resource from storage.
     * @throws PermissionException
     */
    public function destroy(StudentEnrollment $studentEnrollment): JsonResponse
    {
        return $this->studentEnrollmentService->destroyStudentEnrollment($studentEnrollment);
    }

    /**
     * Display a listing of trashed resources.
     * @throws PermissionException
     */
    public function trashed(): JsonResponse
    {
        return $this->studentEnrollmentService->listTrashedStudentEnrollments();
    }

    /**
     * Restore the specified resource from storage.
     * @throws PermissionException
     */
    public function restore($id): JsonResponse
    {
        return $this->studentEnrollmentService->restoreStudentEnrollment($id);
    }

    /**
     * Force delete the specified resource from storage.
     * @throws PermissionException
     */
    public function forceDelete($id): JsonResponse
    {
        return $this->studentEnrollmentService->forceDeleteStudentEnrollment($id);
    }

    /**
     * Get enrollments by student.
     * @throws PermissionException
     */
    public function getByStudent($studentId): JsonResponse
    {
        return $this->studentEnrollmentService->getEnrollmentsByStudent($studentId);
    }

    /**
     * Get enrollments by section.
     * @throws PermissionException
     */
    public function getBySection($sectionId): JsonResponse
    {
        return $this->studentEnrollmentService->getEnrollmentsBySection($sectionId);
    }

    /**
     * Get enrollments by semester.
     * @throws PermissionException
     */
    public function getBySemester($semesterId): JsonResponse
    {
        return $this->studentEnrollmentService->getEnrollmentsBySemester($semesterId);
    }
}
