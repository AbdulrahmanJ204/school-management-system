<?php

namespace App\Services;

use App\Enums\PermissionEnum;
use App\Helpers\ResponseHelper;
use App\Http\Resources\SubjectResource;
use App\Models\Subject;
use App\Exceptions\PermissionException;
use App\Traits\HasPermissionChecks;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class SubjectService
{
    use HasPermissionChecks;

    /**
     * Get list of all subjects.
     * @throws PermissionException
     */
    public function listSubjects(): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_SUBJECTS);

        $subjects = Subject::with([
//            'mainSubject.grade',
        ])->orderBy('name', 'asc')->get();

        return ResponseHelper::jsonResponse(
            SubjectResource::collection($subjects)
        );
    }

    /**
     * Create a new subject.
     * @throws PermissionException
     */
    public function createSubject($request): JsonResponse
    {
        $this->checkPermission(PermissionEnum::CREATE_SUBJECTS);

        $credentials = $request->validated();
        $credentials['created_by'] = auth()->id();

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
                ResponseAlias::HTTP_BAD_REQUEST,
                false
            );
        }

        $subject = Subject::create($credentials);
        $subject->load([
//            'mainSubject.grade',
        ]);

        return ResponseHelper::jsonResponse(
            new SubjectResource($subject),
            __('messages.subject.created'),
            ResponseAlias::HTTP_CREATED,
        );
    }

    /**
     * Show a specific subject.
     * @throws PermissionException
     */
    public function showSubject(Subject $subject): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_SUBJECTS);

        $subject->load([
//            'mainSubject.grade',
        ]);

        return ResponseHelper::jsonResponse(
            new SubjectResource($subject)
        );
    }

    /**
     * Update a subject.
     * @throws PermissionException
     */
    public function updateSubject($request, Subject $subject): JsonResponse
    {
        $this->checkPermission(PermissionEnum::UPDATE_SUBJECTS);

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
                ResponseAlias::HTTP_BAD_REQUEST,
                false
            );
        }

        $subject->update($credentials);
        $subject->load([
//            'mainSubject.grade',
        ]);

        return ResponseHelper::jsonResponse(
            new SubjectResource($subject),
            __('messages.subject.updated')
        );
    }

    /**
     * Delete a subject.
     * @throws PermissionException
     */
    public function destroySubject(Subject $subject): JsonResponse
    {
        $this->checkPermission(PermissionEnum::DELETE_SUBJECTS);

        // Check if subject has related data
        if ($subject->teacherSectionSubjects()->exists()) {
            return ResponseHelper::jsonResponse(
                null,
                __('messages.subject.has_teacher_assignments'),
                ResponseAlias::HTTP_BAD_REQUEST,
                false
            );
        }

        if ($subject->studentMarks()->exists()) {
            return ResponseHelper::jsonResponse(
                null,
                __('messages.subject.has_student_marks'),
                ResponseAlias::HTTP_BAD_REQUEST,
                false
            );
        }

        if ($subject->studyNotes()->exists()) {
            return ResponseHelper::jsonResponse(
                null,
                __('messages.subject.has_study_notes'),
                ResponseAlias::HTTP_BAD_REQUEST,
                false
            );
        }

        $subject->delete();

        return ResponseHelper::jsonResponse(
            null,
            __('messages.subject.deleted')
        );
    }

    /**
     * Get list of trashed subjects.
     * @throws PermissionException
     */
    public function listTrashedSubjects(): JsonResponse
    {
        $this->checkPermission(PermissionEnum::MANAGE_DELETED_SUBJECTS);

        $subjects = Subject::with([
            'mainSubject.grade',
            'createdBy'
        ])->onlyTrashed()->orderBy('name', 'asc')->get();

        return ResponseHelper::jsonResponse(
            SubjectResource::collection($subjects)
        );
    }

    /**
     * Restore a trashed subject.
     * @throws PermissionException
     */
    public function restoreSubject($id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::MANAGE_DELETED_SUBJECTS);

        $subject = Subject::withTrashed()->findOrFail($id);

        if (!$subject->trashed()) {
            return ResponseHelper::jsonResponse(
                null,
                'Subject is not deleted',
                ResponseAlias::HTTP_BAD_REQUEST,
                false
            );
        }

        $subject->restore();
        $subject->load(['mainSubject.grade', 'createdBy']);

        return ResponseHelper::jsonResponse(
            new SubjectResource($subject),
            __('messages.subject.restored')
        );
    }

    /**
     * Force delete a trashed subject.
     * @throws PermissionException
     */
    public function forceDeleteSubject($id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::MANAGE_DELETED_SUBJECTS);

//        $subject = Subject::withTrashed()->findOrFail($id);
        $subject = Subject::findOrFail($id);

        // Check if subject has related data
        if ($subject->teacherSectionSubjects()->exists()) {
            return ResponseHelper::jsonResponse(
                null,
                __('messages.subject.has_teacher_assignments'),
                ResponseAlias::HTTP_BAD_REQUEST,
                false
            );
        }

        if ($subject->studentMarks()->exists()) {
            return ResponseHelper::jsonResponse(
                null,
                __('messages.subject.has_student_marks'),
                ResponseAlias::HTTP_BAD_REQUEST,
                false
            );
        }

        if ($subject->studyNotes()->exists()) {
            return ResponseHelper::jsonResponse(
                null,
                __('messages.subject.has_study_notes'),
                ResponseAlias::HTTP_BAD_REQUEST,
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
