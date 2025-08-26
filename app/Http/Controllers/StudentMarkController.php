<?php

namespace App\Http\Controllers;

use App\Exceptions\PermissionException;
use App\Http\Requests\StudentMarkRequest;
use App\Models\StudentMark;
use App\Services\StudentMarkService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentMarkController extends Controller
{
    protected StudentMarkService $studentMarkService;

    public function __construct(StudentMarkService $studentMarkService)
    {
        $this->studentMarkService = $studentMarkService;
    }

    /**
     * Display a listing of the student marks.
     * @throws PermissionException
     */
    public function index(): JsonResponse
    {
        return $this->studentMarkService->listStudentMarks();
    }

    /**
     * Store a newly created student mark in storage.
     * @throws PermissionException
     */
    public function store(StudentMarkRequest $request): JsonResponse
    {
        return $this->studentMarkService->createStudentMark($request);
    }

    /**
     * Display the specified student mark.
     * @throws PermissionException
     */
    public function show(StudentMark $studentMark): JsonResponse
    {
        return $this->studentMarkService->showStudentMark($studentMark);
    }

    /**
     * Update the specified student mark in storage.
     * @throws PermissionException
     */
    public function update(StudentMarkRequest $request, StudentMark $studentMark): JsonResponse
    {
        return $this->studentMarkService->updateStudentMark($request, $studentMark);
    }

    /**
     * Remove the specified student mark from storage.
     * @throws PermissionException
     */
    public function destroy(StudentMark $studentMark): JsonResponse
    {
        return $this->studentMarkService->destroyStudentMark($studentMark);
    }

    /**
     * Get student marks by student enrollment.
     * @throws PermissionException
     */
    public function getByEnrollment($enrollmentId): JsonResponse
    {
        return $this->studentMarkService->getMarksByEnrollment($enrollmentId);
    }

    /**
     * Get student marks by subject.
     * @throws PermissionException
     */
    public function getBySubject($subjectId): JsonResponse
    {
        return $this->studentMarkService->getMarksBySubject($subjectId);
    }

    /**
     * Get student marks by subject and section.
     * @throws PermissionException
     */
    public function getBySubjectAndSection($subjectId, $sectionId): JsonResponse
    {
        return $this->studentMarkService->getMarksBySubjectAndSection($subjectId, $sectionId);
    }

    /**
     * Get authenticated student's marks for a specific semester
     *
     * @param int $semesterId
     * @return JsonResponse
     */
    public function getMyMarks(int $semesterId): JsonResponse
    {
        return $this->studentMarkService->getMyMarks($semesterId);
    }

    /**
     * Get authenticated student's marks for a specific semester
     *
     * @return JsonResponse
     */
    public function getMyAllMarks(): JsonResponse
    {
        return $this->studentMarkService->getMyAllMarks();
    }
}
