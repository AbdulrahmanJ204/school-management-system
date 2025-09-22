<?php

namespace App\Http\Controllers;

use App\Http\Requests\Assignment\CreateAssignmentRequest;
use App\Http\Requests\Assignment\UpdateAssignmentRequest;
use App\Http\Requests\Assignment\ListTeacherAssignmentRequest;
use App\Services\TeacherAssignmentService;
use Illuminate\Http\JsonResponse;

class TeacherAssignmentController extends Controller
{
    protected TeacherAssignmentService $teacherAssignmentService;

    public function __construct(TeacherAssignmentService $teacherAssignmentService)
    {
        $this->teacherAssignmentService = $teacherAssignmentService;
    }

    /**
     * Create a new assignment
     */
    public function store(CreateAssignmentRequest $request): JsonResponse
    {
        return $this->teacherAssignmentService->createAssignment($request);
    }

    /**
     * Update an existing assignment
     */
    public function update(UpdateAssignmentRequest $request, int $id): JsonResponse
    {
        return $this->teacherAssignmentService->updateAssignment($request, $id);
    }

    /**
     * List all assignments for teacher
     */
    public function index(ListTeacherAssignmentRequest $request): JsonResponse
    {
        return $this->teacherAssignmentService->listAssignments($request);
    }

    /**
     * Delete an assignment
     */
    public function destroy(int $id): JsonResponse
    {
        return $this->teacherAssignmentService->deleteAssignment($id);
    }
}

