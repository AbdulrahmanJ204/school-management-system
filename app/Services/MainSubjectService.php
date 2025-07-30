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
}
