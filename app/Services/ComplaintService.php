<?php

namespace App\Services;

use App\Enums\PermissionEnum;
use App\Exceptions\PermissionException;
use App\Helpers\ResponseHelper;
use App\Http\Resources\ComplaintResource;
use App\Models\Complaint;
use App\Traits\HasPermissionChecks;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ComplaintService
{
    use HasPermissionChecks;

    /**
     * @throws PermissionException
     */
    public function listComplaints(): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_COMPLAINTS);

        $complaints = Complaint::with([
            // 'user',
        ])
            ->orderBy('id', 'desc')
            ->get();

        return ResponseHelper::jsonResponse(
            ComplaintResource::collection($complaints),
            __('messages.complaint.listed')
        );
    }

    /**
     * @throws PermissionException
     */
    public function listTrashedComplaints(): JsonResponse
    {
        $this->checkPermission(PermissionEnum::MANAGE_DELETED_COMPLAINTS);

        $complaints = Complaint::onlyTrashed()
            ->with([
                // 'user',
            ])
            ->orderBy('id', 'desc')
            ->get();

        return ResponseHelper::jsonResponse(
            ComplaintResource::collection($complaints),
            __('messages.complaint.listed')
        );
    }

    /**
     * @throws PermissionException
     */
    public function createComplaint($request): JsonResponse
    {
        $this->checkPermission(PermissionEnum::CREATE_COMPLAINT);

        $complaint = Complaint::create([
            'user_id' => $request->user_id,
            'title' => $request->title,
            'content' => $request->content,
            'answer' => $request->answer,
            'created_by' => auth()->id(),
        ]);

        $complaint->load(['user']);

        return ResponseHelper::jsonResponse(
            new ComplaintResource($complaint),
            __('messages.complaint.created'),
            ResponseAlias::HTTP_CREATED
        );
    }

    /**
     * @throws PermissionException
     */
    public function showComplaint($id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_COMPLAINT);

        $complaint = Complaint::with([
            // 'user',
            ])->findOrFail($id);

        return ResponseHelper::jsonResponse(
            new ComplaintResource($complaint),
            __('messages.complaint.showed')
        );
    }

    /**
     * @throws PermissionException
     */
    public function updateComplaint($request, $id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::UPDATE_COMPLAINT);

        $complaint = Complaint::findOrFail($id);

        $complaint->update([
            'user_id' => $request->user_id,
            'title' => $request->title,
            'content' => $request->content,
            'answer' => $request->answer,
        ]);

        // $complaint->load(['user']);

        return ResponseHelper::jsonResponse(
            new ComplaintResource($complaint),
            __('messages.complaint.updated')
        );
    }

    /**
     * @throws PermissionException
     */
    public function deleteComplaint($id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::DELETE_COMPLAINT);

        $complaint = Complaint::findOrFail($id);
        $complaint->delete();

        return ResponseHelper::jsonResponse(
            null,
            __('messages.complaint.deleted')
        );
    }

    /**
     * @throws PermissionException
     */
    public function restoreComplaint($id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::MANAGE_DELETED_COMPLAINTS);

        $complaint = Complaint::onlyTrashed()->findOrFail($id);
        $complaint->restore();

        return ResponseHelper::jsonResponse(
            new ComplaintResource($complaint),
            __('messages.complaint.restored')
        );
    }

    /**
     * @throws PermissionException
     */
    public function forceDeleteComplaint($id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::MANAGE_DELETED_COMPLAINTS);

        // $complaint = Complaint::onlyTrashed()->findOrFail($id);
        $complaint = Complaint::findOrFail($id);
        $complaint->forceDelete();

        return ResponseHelper::jsonResponse(
            null,
            __('messages.complaint.force_deleted')
        );
    }

    /**
     * @throws PermissionException
     */
    public function getByUser($userId): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_COMPLAINTS);

        $complaints = Complaint::with(['user'])
            ->where('user_id', $userId)
            ->orderBy('id', 'desc')
            ->get();

        return ResponseHelper::jsonResponse(
            ComplaintResource::collection($complaints),
            __('messages.complaint.listed')
        );
    }

    /**
     * @throws PermissionException
     */
    public function answerComplaint($request, $id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::UPDATE_COMPLAINT);

        $complaint = Complaint::findOrFail($id);

        if ($complaint->answer) {
            return ResponseHelper::jsonResponse(
                null,
                __('messages.complaint.already_answered'),
                ResponseAlias::HTTP_BAD_REQUEST
            );
        }

        $complaint->update([
            'answer' => $request->answer,
        ]);

        // $complaint->load(['user']);

        return ResponseHelper::jsonResponse(
            new ComplaintResource($complaint),
            __('messages.complaint.answered')
        );
    }
} 