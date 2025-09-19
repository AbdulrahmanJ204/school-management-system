<?php

namespace App\Services;

use App\Enums\Permissions\ComplaintPermission;
use App\Exceptions\PermissionException;
use App\Helpers\ResponseHelper;
use App\Http\Resources\ComplaintResource;
use App\Models\Complaint;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ComplaintService
{
    

    /**
     * @throws PermissionException
     */
    public function listComplaints(): JsonResponse
    {
        AuthHelper::authorize(ComplaintPermission::VIEW_COMPLAINTS);

        $complaints = Complaint::with([
             'user',
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
        AuthHelper::authorize(ComplaintPermission::MANAGE_DELETED_COMPLAINTS);

        $complaints = Complaint::onlyTrashed()
            ->with([
                 'user',
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
        AuthHelper::authorize(ComplaintPermission::CREATE_COMPLAINT);

        $complaint = Complaint::create([
            'user_id' => $request->user_id,
            'title' => $request->title,
            'content' => $request->content,
            'answer' => $request->answer,
            'created_by' => Auth::user()->id,
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
        AuthHelper::authorize(ComplaintPermission::VIEW_COMPLAINT);

        $complaint = Complaint::with([
                'user',
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
        AuthHelper::authorize(ComplaintPermission::UPDATE_COMPLAINT);

        $complaint = Complaint::findOrFail($id);

        $complaint->update([
            'user_id' => $request->user_id,
            'title' => $request->title,
            'content' => $request->content,
            'answer' => $request->answer,
        ]);

         $complaint->load(['user']);

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
        AuthHelper::authorize(ComplaintPermission::DELETE_COMPLAINT);

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
        AuthHelper::authorize(ComplaintPermission::MANAGE_DELETED_COMPLAINTS);

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
        AuthHelper::authorize(ComplaintPermission::MANAGE_DELETED_COMPLAINTS);

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
        AuthHelper::authorize(ComplaintPermission::VIEW_COMPLAINTS);

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
        AuthHelper::authorize(ComplaintPermission::UPDATE_COMPLAINT);

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

         $complaint->load(['user']);

        return ResponseHelper::jsonResponse(
            new ComplaintResource($complaint),
            __('messages.complaint.answered')
        );
    }
}
