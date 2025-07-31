<?php

namespace App\Services;

use App\Helpers\ResponseHelper;
use App\Http\Resources\MainSubjectResource;
use App\Models\MainSubject;
use App\Exceptions\PermissionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class MainSubjectService
{
    /**
     * Get list of all main subjects.
     */
    public function listMainSubjects(): JsonResponse
    {
        $user = auth()->user();

        if (!$user->hasPermissionTo('عرض المواد الرئيسية')) {
            throw new PermissionException();
        }

        $mainSubjects = MainSubject::with([
            'grade',
            'createdBy',
            'subjects'
        ])->orderBy('name', 'asc')->get();

        return ResponseHelper::jsonResponse(
            MainSubjectResource::collection($mainSubjects)
        );
    }

    /**
     * Create a new main subject.
     */
    public function createMainSubject($request): JsonResponse
    {
        $user = auth()->user();

        if (!$user->hasPermissionTo('انشاء مادة رئيسية')) {
            throw new PermissionException();
        }

        $credentials = $request->validated();
        $credentials['created_by'] = $user->id;

        $mainSubject = MainSubject::create($credentials);
        $mainSubject->load(['grade', 'createdBy']);

        return ResponseHelper::jsonResponse(
            new MainSubjectResource($mainSubject),
            __('messages.main_subject.created'),
            201,
            true
        );
    }

    /**
     * Show a specific main subject.
     */
    public function showMainSubject(MainSubject $mainSubject): JsonResponse
    {
        $user = auth()->user();

        if (!$user->hasPermissionTo('عرض المادة الرئيسية')) {
            throw new PermissionException();
        }

        $mainSubject->load([
            'grade',
            'createdBy',
            'subjects.createdBy'
        ]);

        return ResponseHelper::jsonResponse(
            new MainSubjectResource($mainSubject)
        );
    }

    /**
     * Update a main subject.
     */
    public function updateMainSubject($request, MainSubject $mainSubject): JsonResponse
    {
        $user = auth()->user();

        if (!$user->hasPermissionTo('تعديل مادة رئيسية')) {
            throw new PermissionException();
        }

        $credentials = $request->validated();
        $mainSubject->update($credentials);

        $mainSubject->load(['grade', 'createdBy']);

        return ResponseHelper::jsonResponse(
            new MainSubjectResource($mainSubject),
            __('messages.main_subject.updated')
        );
    }

    /**
     * Delete a main subject.
     */
    public function destroyMainSubject(MainSubject $mainSubject): JsonResponse
    {
        $user = auth()->user();

        if (!$user->hasPermissionTo('حذف مادة رئيسية')) {
            throw new PermissionException();
        }

        // Check if there are related subjects
        if ($mainSubject->subjects()->exists()) {
            return response()->json([
                'message' => 'Cannot delete main subject with existing subjects'
            ], Response::HTTP_CONFLICT);
        }

        $mainSubject->delete();

        return ResponseHelper::jsonResponse(
            null,
            __('messages.main_subject.deleted')
        );
    }

    /**
     * List trashed main subjects.
     */
    public function listTrashedMainSubjects(): JsonResponse
    {
        $user = auth()->user();

        if (!$user->hasPermissionTo('عرض المواد الرئيسية')) {
            throw new PermissionException();
        }

        $mainSubjects = MainSubject::with(['grade', 'createdBy'])
            ->onlyTrashed()
            ->orderBy('name', 'asc')
            ->get();

        return ResponseHelper::jsonResponse(
            MainSubjectResource::collection($mainSubjects)
        );
    }

    /**
     * Restore a main subject.
     */
    public function restoreMainSubject($id): JsonResponse
    {
        $user = auth()->user();

        if (!$user->hasPermissionTo('تعديل مادة رئيسية')) {
            throw new PermissionException();
        }

        $mainSubject = MainSubject::withTrashed()->findOrFail($id);
        
        if (!$mainSubject->trashed()) {
            return ResponseHelper::jsonResponse(
                null,
                'Main subject is not deleted',
                400,
                false
            );
        }

        $mainSubject->restore();

        return ResponseHelper::jsonResponse(
            new MainSubjectResource($mainSubject),
            __('messages.main_subject.restored')
        );
    }

    /**
     * Force delete a main subject.
     */
    public function forceDeleteMainSubject($id): JsonResponse
    {
        $user = auth()->user();

        if (!$user->hasPermissionTo('حذف مادة رئيسية')) {
            throw new PermissionException();
        }

        $mainSubject = MainSubject::withTrashed()->findOrFail($id);
        
        // Check if there are related subjects
        if ($mainSubject->subjects()->exists()) {
            return ResponseHelper::jsonResponse(
                null,
                __('messages.main_subject.cannot_delete_with_subjects'),
                400,
                false
            );
        }

        $mainSubject->forceDelete();

        return ResponseHelper::jsonResponse(
            null,
            __('messages.main_subject.force_deleted')
        );
    }
}
