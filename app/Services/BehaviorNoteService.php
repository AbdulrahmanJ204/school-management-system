<?php

namespace App\Services;

use App\Enums\PermissionEnum;
use App\Exceptions\PermissionException;
use App\Helpers\ResponseHelper;
use App\Http\Resources\BehaviorNoteResource;
use App\Models\BehaviorNote;
use App\Traits\HasPermissionChecks;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class BehaviorNoteService
{
    use HasPermissionChecks;

    /**
     * @throws PermissionException
     */
    public function listBehaviorNotes(): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_BEHAVIOR_NOTES);

        $behaviorNotes = BehaviorNote::with([
           'student',
           'schoolDay',
        ])
            ->orderBy('id', 'desc')
            ->paginate(50);

        return ResponseHelper::jsonResponse(
            BehaviorNoteResource::collection($behaviorNotes),
            __('messages.behavior_note.listed'),
            200,
            true,
            $behaviorNotes->lastPage()
        );
    }

    /**
     * @throws PermissionException
     */
    public function listTrashedBehaviorNotes(): JsonResponse
    {
        $this->checkPermission(PermissionEnum::MANAGE_DELETED_BEHAVIOR_NOTES);

        $behaviorNotes = BehaviorNote::onlyTrashed()
            ->with([
                'student',
                'schoolDay',
            ])
            ->orderBy('id', 'desc')
            ->paginate(50);

        return ResponseHelper::jsonResponse(
            BehaviorNoteResource::collection($behaviorNotes),
            __('messages.behavior_note.listed'),
            200,
            true,
            $behaviorNotes->lastPage()
        );
    }

    /**
     * @throws PermissionException
     */
    public function createBehaviorNote($request): JsonResponse
    {
        $this->checkPermission(PermissionEnum::CREATE_BEHAVIOR_NOTE);

        $data = $request->validated();
        $data['created_by'] = auth()->id();

        $behaviorNote = BehaviorNote::create($data);

        return ResponseHelper::jsonResponse(
            new BehaviorNoteResource($behaviorNote->load([
                'student',
                'schoolDay',
            ])),
            __('messages.behavior_note.created'),
            ResponseAlias::HTTP_CREATED,
        );
    }

    /**
     * @throws PermissionException
     */
    public function showBehaviorNote($id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_BEHAVIOR_NOTE);

        $behaviorNote = BehaviorNote::with([
            'student',
            'schoolDay',
        ])
            ->findOrFail($id);

        return ResponseHelper::jsonResponse(
            new BehaviorNoteResource($behaviorNote),
        );
    }

    /**
     * @throws PermissionException
     */
    public function updateBehaviorNote($request, $id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::UPDATE_BEHAVIOR_NOTE);

        $behaviorNote = BehaviorNote::findOrFail($id);
        $data = $request->validated();

        $behaviorNote->update($data);

        return ResponseHelper::jsonResponse(
            new BehaviorNoteResource($behaviorNote->load([
                'student',
                'schoolDay',
            ])),
            __('messages.behavior_note.updated'),
        );
    }

    /**
     * @throws PermissionException
     */
    public function deleteBehaviorNote($id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::DELETE_BEHAVIOR_NOTE);

        $behaviorNote = BehaviorNote::findOrFail($id);
        $behaviorNote->delete();

        return ResponseHelper::jsonResponse(
            null,
            __('messages.behavior_note.deleted'),
        );
    }

    /**
     * @throws PermissionException
     */
    public function restoreBehaviorNote($id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::MANAGE_DELETED_BEHAVIOR_NOTES);

        $behaviorNote = BehaviorNote::onlyTrashed()->findOrFail($id);
        $behaviorNote->restore();

        return ResponseHelper::jsonResponse(
            new BehaviorNoteResource($behaviorNote->load([
                'student',
                'schoolDay',
            ])),
            __('messages.behavior_note.restore'),
        );
    }

    /**
     * @throws PermissionException
     */
    public function forceDeleteBehaviorNote($id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::MANAGE_DELETED_BEHAVIOR_NOTES);

//        $behaviorNote = BehaviorNote::onlyTrashed()->findOrFail($id);
        $behaviorNote = BehaviorNote::findOrFail($id);
        $behaviorNote->forceDelete();

        return ResponseHelper::jsonResponse(
            null,
            __('messages.behavior_note.force_deleted'),
        );
    }

    /**
     * @throws PermissionException
     */
    public function getByStudent($studentId): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_BEHAVIOR_NOTES);

        $behaviorNotes = BehaviorNote::where('student_id', $studentId)
            ->with([
                'student',
                'schoolDay',
            ])
            ->orderBy('id', 'desc')
            ->paginate(50);

        return ResponseHelper::jsonResponse(
            BehaviorNoteResource::collection($behaviorNotes),
            __('messages.behavior_note.listed'),
            200,
            true,
            $behaviorNotes->lastPage()
        );
    }

    /**
     * @throws PermissionException
     */
    public function getBySchoolDay($schoolDayId): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_BEHAVIOR_NOTES);

        $behaviorNotes = BehaviorNote::where('school_day_id', $schoolDayId)
            ->with([
                'student',
                'schoolDay',
            ])
            ->orderBy('id', 'desc')
            ->paginate(50);

        return ResponseHelper::jsonResponse(
            BehaviorNoteResource::collection($behaviorNotes),
            __('messages.behavior_note.listed'),
            200,
            true,
            $behaviorNotes->lastPage()
        );
    }
}
