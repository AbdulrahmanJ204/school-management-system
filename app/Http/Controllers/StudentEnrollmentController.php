<?php

namespace App\Http\Controllers;

use App\Exceptions\PermissionException;
use App\Http\Requests\StudentEnrollment\ListStudentEnrollmentRequest;
use App\Http\Requests\StudentEnrollment\StoreStudentEnrollmentRequest;
use App\Http\Requests\StudentEnrollment\UpdateStudentEnrollmentRequest;
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
     * @throws PermissionException
     */
    public function index(ListStudentEnrollmentRequest $request): JsonResponse
    {
        return $this->studentEnrollmentService->listStudentEnrollments($request);
    }

    /**
     * @throws PermissionException
     */
    public function store(StoreStudentEnrollmentRequest $request): JsonResponse
    {
        return $this->studentEnrollmentService->createStudentEnrollment($request);
    }

    /**
     * @throws PermissionException
     */
    public function show(StudentEnrollment $studentEnrollment): JsonResponse
    {
        return $this->studentEnrollmentService->showStudentEnrollment($studentEnrollment);
    }

    /**
     * @throws PermissionException
     */
    public function update(UpdateStudentEnrollmentRequest $request, StudentEnrollment $studentEnrollment): JsonResponse
    {
        return $this->studentEnrollmentService->updateStudentEnrollment($request, $studentEnrollment);
    }

    /**
     * @throws PermissionException
     */
    public function destroy(StudentEnrollment $studentEnrollment): JsonResponse
    {
        return $this->studentEnrollmentService->destroyStudentEnrollment($studentEnrollment);
    }

    /**
     * @throws PermissionException
     */
    public function trashed(): JsonResponse
    {
        return $this->studentEnrollmentService->listTrashedStudentEnrollments();
    }

    /**
     * @throws PermissionException
     */
    public function restore($id): JsonResponse
    {
        return $this->studentEnrollmentService->restoreStudentEnrollment($id);
    }

    /**
     * @throws PermissionException
     */
    public function forceDelete($id): JsonResponse
    {
        return $this->studentEnrollmentService->forceDeleteStudentEnrollment($id);
    }
}
