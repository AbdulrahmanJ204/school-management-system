<?php

namespace App\Services;

use App\Helpers\ResponseHelper;
use App\Http\Resources\SubjectResource;
use App\Models\Subject;
use App\Exceptions\PermissionException;
use Illuminate\Http\Response;

class SubjectService
{
    /**
     * Get list of all subjects.
     */
    public function listSubjects()
    {
        $user = auth()->user();

        if (!$user->hasPermissionTo('عرض المواد')) {
            throw new PermissionException();
        }

        $subjects = Subject::with([
            'mainSubject.grade',
            'createdBy'
        ])->orderBy('name', 'asc')->get();

        return ResponseHelper::jsonResponse(
            SubjectResource::collection($subjects)
        );
    }

    /**
     * Create a new subject.
     */
    public function createSubject($request)
    {
        $user = auth()->user();

        if (!$user->hasPermissionTo('انشاء مادة')) {
            throw new PermissionException();
        }

        $credentials = $request->validated();
        $credentials['created_by'] = $user->id;

        // Validate that percentages sum to 100
        $totalPercentage = $credentials['homework_percentage'] +
            $credentials['oral_percentage'] +
            $credentials['activity_percentage'] +
            $credentials['quiz_percentage'] +
            $credentials['exam_percentage'];

        if ($totalPercentage !== 100) {
            return ResponseHelper::jsonResponse(
                null,
                __('messages.subject.percentage_sum_error'),
                400,
                false
            );
        }

        $subject = Subject::create($credentials);
        $subject->load(['mainSubject.grade', 'createdBy']);

        return ResponseHelper::jsonResponse(
            new SubjectResource($subject),
            __('messages.subject.created'),
            201,
            true
        );
    }

    /**
     * Show a specific subject.
     */
    public function showSubject(Subject $subject)
    {
        $user = auth()->user();

        if (!$user->hasPermissionTo('عرض المادة')) {
            throw new PermissionException();
        }

        $subject->load([
            'mainSubject.grade',
            'createdBy'
        ]);

        return ResponseHelper::jsonResponse(
            new SubjectResource($subject)
        );
    }

    /**
     * Update a subject.
     */
    public function updateSubject($request, Subject $subject)
    {
        $user = auth()->user();

        if (!$user->hasPermissionTo('تعديل مادة')) {
            throw new PermissionException();
        }

        $credentials = $request->validated();

        // Validate that percentages sum to 100
        $totalPercentage = $credentials['homework_percentage'] +
            $credentials['oral_percentage'] +
            $credentials['activity_percentage'] +
            $credentials['quiz_percentage'] +
            $credentials['exam_percentage'];

        if ($totalPercentage !== 100) {
            return ResponseHelper::jsonResponse(
                null,
                __('messages.subject.percentage_sum_error'),
                400,
                false
            );
        }

        $subject->update($credentials);
        $subject->load(['mainSubject.grade', 'createdBy']);

        return ResponseHelper::jsonResponse(
            new SubjectResource($subject),
            __('messages.subject.updated')
        );
    }

    /**
     * Delete a subject.
     */
    public function destroySubject(Subject $subject)
    {
        $user = auth()->user();

        if (!$user->hasPermissionTo('حذف مادة')) {
            throw new PermissionException();
        }

        // Check if there are related records
        if ($subject->teacherSectionSubjects()->exists() ||
            $subject->quizTargets()->exists() ||
            $subject->assignments()->exists() ||
            $subject->studentMarks()->exists() ||
            $subject->studyNotes()->exists() ||
            $subject->files()->exists()) {

            return response()->json([
                'message' => 'Cannot delete subject with existing related data'
            ], Response::HTTP_CONFLICT);
        }

        $subject->delete();

        return ResponseHelper::jsonResponse(
            null,
            __('messages.subject.deleted')
        );
    }

    /**
     * List trashed subjects.
     */
    public function listTrashedSubjects()
    {
        $user = auth()->user();

        if (!$user->hasPermissionTo('عرض المواد')) {
            throw new PermissionException();
        }

        $subjects = Subject::with([
            'mainSubject.grade',
            'createdBy'
        ])
            ->onlyTrashed()
            ->orderBy('name', 'asc')
            ->get();

        return ResponseHelper::jsonResponse(
            SubjectResource::collection($subjects)
        );
    }

    /**
     * Restore a subject.
     */
    public function restoreSubject($id)
    {
        $user = auth()->user();

        if (!$user->hasPermissionTo('تعديل مادة')) {
            throw new PermissionException();
        }

        $subject = Subject::withTrashed()->findOrFail($id);
        
        if (!$subject->trashed()) {
            return ResponseHelper::jsonResponse(
                null,
                'Subject is not deleted',
                400,
                false
            );
        }

        $subject->restore();

        return ResponseHelper::jsonResponse(
            new SubjectResource($subject),
            __('messages.subject.restored')
        );
    }

    /**
     * Force delete a subject.
     */
    public function forceDeleteSubject($id)
    {
        $user = auth()->user();

        if (!$user->hasPermissionTo('حذف مادة')) {
            throw new PermissionException();
        }

        $subject = Subject::withTrashed()->findOrFail($id);
        
        // Check if there are related records
        if ($subject->teacherSectionSubjects()->exists() ||
            $subject->quizTargets()->exists() ||
            $subject->assignments()->exists() ||
            $subject->studentMarks()->exists() ||
            $subject->studyNotes()->exists() ||
            $subject->files()->exists()) {

            return ResponseHelper::jsonResponse(
                null,
                __('messages.subject.cannot_delete_with_relations'),
                400,
                false
            );
        }

        $subject->forceDelete();

        return ResponseHelper::jsonResponse(
            null,
            __('messages.subject.force_deleted')
        );
    }
}
