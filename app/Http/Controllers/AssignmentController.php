<?php

namespace App\Http\Controllers;

use App\Http\Requests\Assignment\ListAssignmentRequest;
use App\Http\Requests\Assignment\StoreAssignmentRequest;
use App\Http\Requests\Assignment\UpdateAssignmentRequest;
use App\Models\Assignment;
use App\Services\AssignmentService;
use Illuminate\Http\JsonResponse;

class AssignmentController extends Controller
{
    protected $assignmentService;

    public function __construct(AssignmentService $assignmentService)
    {
        $this->assignmentService = $assignmentService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(ListAssignmentRequest $request): ?JsonResponse
    {
        return $this->assignmentService->listAssignments($request);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAssignmentRequest $request)
    {
        return $this->assignmentService->createAssignment($request);
    }

    /**
     * Display the specified resource.
     */
    public function show($assignmentId): JsonResponse
    {
        return $this->assignmentService->showAssignment($assignmentId);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAssignmentRequest $request, Assignment $assignment)
    {
        return $this->assignmentService->updateAssignment($request, $assignment->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Assignment $assignment): JsonResponse
    {
        return $this->assignmentService->deleteAssignment($assignment->id);
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore($assignmentId): JsonResponse
    {
        return $this->assignmentService->restoreAssignment($assignmentId);
    }

    /**
     * Permanently delete the specified resource from storage.
     */
    public function delete($assignmentId): JsonResponse
    {
        return $this->assignmentService->forceDeleteAssignment($assignmentId);
    }
}
