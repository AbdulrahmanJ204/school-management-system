<?php

namespace App\Services;

use App\Enums\Permissions\AssignmentPermission;
use App\Exceptions\PermissionException;
use App\Helpers\AuthHelper;
use App\Helpers\ResponseHelper;
use App\Http\Resources\AssignmentResource;
use App\Models\Assignment;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class AssignmentService
{
    

    /**
     * @throws PermissionException
     */
    public function listAssignments(Request $request): JsonResponse
    {
        AuthHelper::authorize(AssignmentPermission::VIEW_ASSIGNMENTS);

        $query = Assignment::with([
            'assignedSession',
            'dueSession',
            'subject',
            'section.grade',
            'createdBy'
        ]);

        // Apply filters
        if ($request->has('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->has('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('assigned_session_id')) {
            $query->where('assigned_session_id', $request->assigned_session_id);
        }

        if ($request->has('due_session_id')) {
            $query->where('due_session_id', $request->due_session_id);
        }

        if ($request->has('date_from')) {
            $query->whereHas('assignedSession', function ($q) use ($request) {
                $q->where('date', '>=', $request->date_from);
            });
        }

        if ($request->has('date_to')) {
            $query->whereHas('assignedSession', function ($q) use ($request) {
                $q->where('date', '<=', $request->date_to);
            });
        }

        $assignments = $query->orderBy('created_at', 'desc')->paginate(15);

        return ResponseHelper::jsonResponse(
            AssignmentResource::collection($assignments),
            'تم عرض الواجبات بنجاح'
        );
    }

    /**
     * @throws PermissionException
     */
    public function listTrashedAssignments(): JsonResponse
    {
        AuthHelper::authorize(AssignmentPermission::MANAGE_DELETED_ASSIGNMENTS);

        $assignments = Assignment::onlyTrashed()
            ->with([
                'assignedSession',
                'dueSession',
                'subject',
                'section.grade',
                'createdBy'
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return ResponseHelper::jsonResponse(
            AssignmentResource::collection($assignments),
            'تم عرض الواجبات المحذوفة بنجاح'
        );
    }

    /**
     * @throws PermissionException
     */
    public function createAssignment(Request $request): JsonResponse
    {
        AuthHelper::authorize(AssignmentPermission::CREATE_ASSIGNMENT);

        $data = $request->validated();
        $data['created_by'] = Auth::user()->id();

        // Handle photo upload if provided
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('assignments', 'public');
        }

        $assignment = Assignment::create($data);

        return ResponseHelper::jsonResponse(
            new AssignmentResource($assignment->load([
                'assignedSession',
                'dueSession',
                'subject',
                'section.grade',
                'createdBy'
            ])),
            'تم إنشاء الواجب بنجاح',
            ResponseAlias::HTTP_CREATED,
        );
    }

    /**
     * @throws PermissionException
     */
    public function showAssignment($id): JsonResponse
    {
        AuthHelper::authorize(AssignmentPermission::VIEW_ASSIGNMENT);

        $assignment = Assignment::with([
            'assignedSession',
            'dueSession',
            'subject',
            'section.grade',
            'createdBy'
        ])
            ->findOrFail($id);

        return ResponseHelper::jsonResponse(
            new AssignmentResource($assignment),
            'تم عرض الواجب بنجاح'
        );
    }

    /**
     * @throws PermissionException
     */
    public function updateAssignment(Request $request, $id): JsonResponse
    {
        AuthHelper::authorize(AssignmentPermission::UPDATE_ASSIGNMENT);

        $assignment = Assignment::findOrFail($id);
        $data = $request->validated();

        // Handle photo upload if provided
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($assignment->photo) {
                Storage::disk('public')->delete($assignment->photo);
            }
            $data['photo'] = $request->file('photo')->store('assignments', 'public');
        }

        $assignment->update($data);

        return ResponseHelper::jsonResponse(
            new AssignmentResource($assignment->load([
                'assignedSession',
                'dueSession',
                'subject',
                'section.grade',
                'createdBy'
            ])),
            'تم تحديث الواجب بنجاح'
        );
    }

    /**
     * @throws PermissionException
     */
    public function deleteAssignment($id): JsonResponse
    {
        AuthHelper::authorize(AssignmentPermission::DELETE_ASSIGNMENT);

        $assignment = Assignment::findOrFail($id);
        $assignment->delete();

        return ResponseHelper::jsonResponse(
            null,
            'تم حذف الواجب بنجاح'
        );
    }

    /**
     * @throws PermissionException
     */
    public function restoreAssignment($id): JsonResponse
    {
        AuthHelper::authorize(AssignmentPermission::MANAGE_DELETED_ASSIGNMENTS);

        $assignment = Assignment::onlyTrashed()->findOrFail($id);
        $assignment->restore();

        return ResponseHelper::jsonResponse(
            new AssignmentResource($assignment->load([
                'assignedSession',
                'dueSession',
                'subject',
                'section.grade',
                'createdBy'
            ])),
            'تم استعادة الواجب بنجاح'
        );
    }

    /**
     * @throws PermissionException
     */
    public function forceDeleteAssignment($id): JsonResponse
    {
        AuthHelper::authorize(AssignmentPermission::MANAGE_DELETED_ASSIGNMENTS);

        $assignment = Assignment::onlyTrashed()->findOrFail($id);
        
        // Delete photo if exists
        if ($assignment->photo) {
            Storage::disk('public')->delete($assignment->photo);
        }
        
        $assignment->forceDelete();

        return ResponseHelper::jsonResponse(
            null,
            'تم حذف الواجب نهائياً بنجاح'
        );
    }

    /**
     * @throws PermissionException
     */
    public function getBySubject($subjectId): JsonResponse
    {
        AuthHelper::authorize(AssignmentPermission::VIEW_ASSIGNMENTS);

        $assignments = Assignment::where('subject_id', $subjectId)
            ->with([
                'assignedSession',
                'dueSession',
                'subject',
                'section.grade',
                'createdBy'
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return ResponseHelper::jsonResponse(
            AssignmentResource::collection($assignments),
            'تم عرض واجبات المادة بنجاح'
        );
    }

    /**
     * @throws PermissionException
     */
    public function getBySection($sectionId): JsonResponse
    {
        AuthHelper::authorize(AssignmentPermission::VIEW_ASSIGNMENTS);

        $assignments = Assignment::where('section_id', $sectionId)
            ->with([
                'assignedSession',
                'dueSession',
                'subject',
                'section.grade',
                'createdBy'
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return ResponseHelper::jsonResponse(
            AssignmentResource::collection($assignments),
            'تم عرض واجبات الشعبة بنجاح'
        );
    }

    /**
     * @throws PermissionException
     */
    public function getByType($type): JsonResponse
    {
        AuthHelper::authorize(AssignmentPermission::VIEW_ASSIGNMENTS);

        $assignments = Assignment::where('type', $type)
            ->with([
                'assignedSession',
                'dueSession',
                'subject',
                'section.grade',
                'createdBy'
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return ResponseHelper::jsonResponse(
            AssignmentResource::collection($assignments),
            'تم عرض الواجبات حسب النوع بنجاح'
        );
    }
}
