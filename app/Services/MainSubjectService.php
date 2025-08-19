<?php

namespace App\Services;

use App\Enums\PermissionEnum;
use App\Helpers\ResponseHelper;
use App\Http\Resources\MainSubjectResource;
use App\Models\MainSubject;
use App\Exceptions\PermissionException;
use App\Models\Subject;
use App\Traits\HasPermissionChecks;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class MainSubjectService
{
    use HasPermissionChecks;

    /**
     * Get list of all main subjects.
     * @throws PermissionException
     */
    public function listMainSubjects(): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_MAIN_SUBJECTS);

        $mainSubjects = MainSubject::with([
            'grade',
            'subjects'
        ])->orderBy('name', 'asc')->get();

        return ResponseHelper::jsonResponse(
            MainSubjectResource::collection($mainSubjects)
        );
    }

    /**
     * Create a new main subject.
     * @throws PermissionException
     */
    public function createMainSubject($request): JsonResponse
    {
        $this->checkPermission(PermissionEnum::CREATE_MAIN_SUBJECT);

        $credentials = $request->validated();
        $credentials['created_by'] = auth()->id();

        $mainSubject = MainSubject::create($credentials);
        $mainSubject->load([
            'grade',
        ]);

        return ResponseHelper::jsonResponse(
            new MainSubjectResource($mainSubject),
            __('messages.main_subject.created'),
            ResponseAlias::HTTP_CREATED,
            true
        );
    }

    /**
     * Show a specific main subject.
     * @throws PermissionException
     */
    public function showMainSubject(MainSubject $mainSubject): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_MAIN_SUBJECT);

        $mainSubject->load([
            'grade',
            'subjects'
        ]);

        return ResponseHelper::jsonResponse(
            new MainSubjectResource($mainSubject)
        );
    }

    /**
     * Update a main subject.
     * @throws PermissionException
     */
    public function updateMainSubject($request, MainSubject $mainSubject): JsonResponse
    {
        $this->checkPermission(PermissionEnum::UPDATE_MAIN_SUBJECT);

        $credentials = $request->validated();
        $mainSubject->update($credentials);

        $mainSubject->load([
            'grade',
        ]);

        return ResponseHelper::jsonResponse(
            new MainSubjectResource($mainSubject),
            __('messages.main_subject.updated')
        );
    }

    /**
     * Delete a main subject.
     * @throws PermissionException
     */
    public function destroyMainSubject(MainSubject $mainSubject): JsonResponse
    {
        $this->checkPermission(PermissionEnum::DELETE_MAIN_SUBJECT);

        // Check if there are related subjects
        if ($mainSubject->subjects()->exists()) {
            return ResponseHelper::jsonResponse(
                null,
                'Cannot delete main subject with existing subjects',
                ResponseAlias::HTTP_CONFLICT,
                false
            );
        }

        $mainSubject->delete();

        return ResponseHelper::jsonResponse(
            null,
            __('messages.main_subject.deleted')
        );
    }

    /**
     * List trashed main subjects.
     * @throws PermissionException
     */
    public function listTrashedMainSubjects(): JsonResponse
    {
        $this->checkPermission(PermissionEnum::MANAGE_DELETED_MAIN_SUBJECTS);

        $mainSubjects = MainSubject::with([
            'grade',
        ])
            ->onlyTrashed()
            ->orderBy('name', 'asc')
            ->get();

        return ResponseHelper::jsonResponse(
            MainSubjectResource::collection($mainSubjects)
        );
    }

    /**
     * Restore a main subject.
     * @throws PermissionException
     */
    public function restoreMainSubject($id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::MANAGE_DELETED_MAIN_SUBJECTS);

        $mainSubject = MainSubject::withTrashed()->findOrFail($id);

        if (!$mainSubject->trashed()) {
            return ResponseHelper::jsonResponse(
                null,
                'Main subject is not deleted',
                ResponseAlias::HTTP_CONFLICT,
                false
            );
        }

        $mainSubject->restore();
        $mainSubject->load([
            'grade',
        ]);

        return ResponseHelper::jsonResponse(
            new MainSubjectResource($mainSubject),
            __('messages.main_subject.restored')
        );
    }

    /**
     * Force delete a main subject.
     * @throws PermissionException
     */
    public function forceDeleteMainSubject($id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::MANAGE_DELETED_MAIN_SUBJECTS);

        $mainSubject = MainSubject::withTrashed()->findOrFail($id);

        // Check if there are related subjects
        if ($mainSubject->subjects()->exists()) {
            return ResponseHelper::jsonResponse(
                null,
                __('messages.main_subject.cannot_delete_with_subjects'),
                ResponseAlias::HTTP_CONFLICT,
                false
            );
        }

        $mainSubject->forceDelete();

        return ResponseHelper::jsonResponse(
            null,
            __('messages.main_subject.force_deleted')
        );
    }

    /**
     * Create a main subject with a single subject.
     * @throws PermissionException
     * @throws Exception
     */
    public function createMainSubjectWithSubject($request): JsonResponse
    {
        $this->checkAllPermissions([PermissionEnum::CREATE_SUBJECTS,PermissionEnum::CREATE_MAIN_SUBJECT]);

        $data = $request->validated();

        // Start database transaction
        \DB::beginTransaction();

        try {
            // Create main subject
            $mainSubjectData = [
                'grade_id' => $data['grade_id'],
                'name' => $data['name'],
                'code' => $data['code'],
                'success_rate' => $data['success_rate'],
                'created_by' => auth()->id()
            ];

            $mainSubject = MainSubject::create($mainSubjectData);

            // Create subject
            $subjectData = [
                'name' => $data['subject_name'],
                'main_subject_id' => $mainSubject->id,
                'code' => $data['subject_code'],
                'full_mark' => $data['full_mark'],
                'homework_percentage' => $data['homework_percentage'],
                'oral_percentage' => $data['oral_percentage'],
                'activity_percentage' => $data['activity_percentage'],
                'quiz_percentage' => $data['quiz_percentage'],
                'exam_percentage' => $data['exam_percentage'],
                'num_class_period' => $data['num_class_period'],
                'is_failed' => $data['is_failed'] ?? false,
                'created_by' => auth()->id()
            ];

            Subject::create($subjectData);

            // Load relationships
            $mainSubject->load([
                'grade',
                'subjects'
            ]);

            \DB::commit();

            return ResponseHelper::jsonResponse(
                [
                    'main_subject' => new MainSubjectResource($mainSubject),
//                    'subject' => new SubjectResource($subject)
                ],
                __('messages.subject.create_main_subject_with_subject'),
                ResponseAlias::HTTP_CREATED,
                true
            );

        } catch (Exception $e) {
            \DB::rollBack();
            throw $e;
        }
    }
}
