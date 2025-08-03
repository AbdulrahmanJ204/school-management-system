<?php

namespace App\Http\Controllers;

use App\Exceptions\PermissionException;
use App\Http\Requests\BaseRequest;
use App\Http\Requests\ComplaintRequest;
use App\Services\ComplaintService;
use Illuminate\Http\JsonResponse;

class ComplaintController extends Controller
{
    protected ComplaintService $complaintService;

    public function __construct(ComplaintService $complaintService)
    {
        $this->complaintService = $complaintService;
    }

    /**
     * @throws PermissionException
     */
    public function index(): JsonResponse
    {
        return $this->complaintService->listComplaints();
    }

    /**
     * @throws PermissionException
     */
    public function trashed(): JsonResponse
    {
        return $this->complaintService->listTrashedComplaints();
    }

    /**
     * @throws PermissionException
     */
    public function store(ComplaintRequest $request): JsonResponse
    {
        return $this->complaintService->createComplaint($request);
    }

    /**
     * @throws PermissionException
     */
    public function show($id): JsonResponse
    {
        return $this->complaintService->showComplaint($id);
    }

    /**
     * @throws PermissionException
     */
    public function update(ComplaintRequest $request, $id): JsonResponse
    {
        return $this->complaintService->updateComplaint($request, $id);
    }

    /**
     * @throws PermissionException
     */
    public function destroy($id): JsonResponse
    {
        return $this->complaintService->deleteComplaint($id);
    }

    /**
     * @throws PermissionException
     */
    public function restore($id): JsonResponse
    {
        return $this->complaintService->restoreComplaint($id);
    }

    /**
     * @throws PermissionException
     */
    public function forceDelete($id): JsonResponse
    {
        return $this->complaintService->forceDeleteComplaint($id);
    }

    /**
     * @throws PermissionException
     */
    public function getByUser($userId): JsonResponse
    {
        return $this->complaintService->getByUser($userId);
    }

    /**
     * @throws PermissionException
     */
    public function answer(BaseRequest $request, $id): JsonResponse
    {
        return $this->complaintService->answerComplaint($request, $id);
    }
}
