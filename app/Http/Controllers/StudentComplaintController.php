<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudentComplaintRequest;
use App\Services\StudentComplaintService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentComplaintController extends Controller
{
    protected StudentComplaintService $studentComplaintService;

    public function __construct(StudentComplaintService $studentComplaintService)
    {
        $this->studentComplaintService = $studentComplaintService;
    }

    /**
     * Create a new complaint
     *
     * @param StudentComplaintRequest $request
     * @return JsonResponse
     */
    public function store(StudentComplaintRequest $request): JsonResponse
    {
        $userId = Auth::user()->id;
        
        return $this->studentComplaintService->createStudentComplaint($userId, [
            'title' => $request->title,
            'content' => $request->content,
        ]);
    }

    /**
     * Update existing complaint
     *
     * @param StudentComplaintRequest $request
     * @return JsonResponse
     */
    public function update(StudentComplaintRequest $request): JsonResponse
    {
        $userId = Auth::user()->id;
        
        return $this->studentComplaintService->updateStudentComplaint($userId, $request->id, [
            'title' => $request->title,
            'content' => $request->content,
        ]);
    }

    /**
     * Delete complaint
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $userId = Auth::user()->id;
        
        return $this->studentComplaintService->deleteStudentComplaint($userId, $id);
    }

    /**
     * Get all complaints for authenticated student
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $userId = Auth::user()->id;
        
        return $this->studentComplaintService->getStudentComplaints($userId);
    }
}
