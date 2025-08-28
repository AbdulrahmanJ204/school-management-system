<?php

namespace App\Http\Controllers;

use App\Http\Requests\Assignment\ListStudentAssignmentRequest;
use App\Services\StudentAssignmentService;
use Illuminate\Http\JsonResponse;

class StudentAssignmentController extends Controller
{
    protected StudentAssignmentService $studentAssignmentService;

    public function __construct(StudentAssignmentService $studentAssignmentService)
    {
        $this->studentAssignmentService = $studentAssignmentService;
    }

    /**
     * List assignments for student's section
     */
    public function index(ListStudentAssignmentRequest $request): JsonResponse
    {
        return $this->studentAssignmentService->listAssignments($request);
    }
}

