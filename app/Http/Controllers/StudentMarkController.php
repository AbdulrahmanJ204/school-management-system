<?php

namespace App\Http\Controllers;

use App\Exceptions\PermissionException;
use App\Http\Requests\StudentMark\StoreStudentMarkRequest;
use App\Http\Requests\StudentMark\UpdateStudentMarkRequest;
use App\Http\Requests\StudentMark\ListStudentMarkRequest;
use App\Http\Requests\StudentMark\BulkStoreStudentMarkRequest;
use App\Http\Requests\StudentMark\BulkUpdateStudentMarkRequest;
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
    public function index(ListStudentMarkRequest $request): JsonResponse
    {
        return $this->studentMarkService->listStudentMarks($request);
    }

    /**
     * Store a newly created student mark in storage.
     * @throws PermissionException
     */
    public function store(StoreStudentMarkRequest $request): JsonResponse
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
    public function update(UpdateStudentMarkRequest $request, StudentMark $studentMark): JsonResponse
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

    /**
     * Store multiple student marks in bulk.
     * @throws PermissionException
     */
    public function bulkStore(BulkStoreStudentMarkRequest $request): JsonResponse
    {
        return $this->studentMarkService->bulkCreateStudentMarks($request);
    }

    /**
     * Update multiple student marks in bulk.
     * @throws PermissionException
     */
    public function bulkUpdate(BulkUpdateStudentMarkRequest $request): JsonResponse
    {
        return $this->studentMarkService->bulkUpdateStudentMarks($request);
    }
}
