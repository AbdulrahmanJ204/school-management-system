<?php

namespace App\Services;

use App\Enums\PermissionEnum;
use App\Exceptions\PermissionException;
use App\Helpers\ResponseHelper;
use App\Http\Resources\StudyNoteResource;
use App\Models\StudyNote;
use App\Traits\HasPermissionChecks;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class StudyNoteService
{
    use HasPermissionChecks;

    /**
     * @throws PermissionException
     */
    public function listStudyNotes(): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_STUDY_NOTES);

        $studyNotes = StudyNote::with([
            'student',
            'schoolDay',
            'subject',
        ])
            ->orderBy('id', 'desc')
            ->paginate(50);

        return ResponseHelper::jsonResponse(
            StudyNoteResource::collection($studyNotes),
            __('messages.study_note.listed'),
            200,
            true,
            $studyNotes->lastPage()
        );
    }

    /**
     * @throws PermissionException
     */
    public function listTrashedStudyNotes(): JsonResponse
    {
        $this->checkPermission(PermissionEnum::MANAGE_DELETED_STUDY_NOTES);

        $studyNotes = StudyNote::onlyTrashed()
            ->with([
                'student',
                'schoolDay',
                'subject',
            ])
            ->orderBy('id', 'desc')
            ->paginate(50);

        return ResponseHelper::jsonResponse(
            StudyNoteResource::collection($studyNotes),
            __('messages.study_note.listed'),
            200,
            true,
            $studyNotes->lastPage()
        );
    }

    /**
     * @throws PermissionException
     */
    public function createStudyNote($request): JsonResponse
    {
        $this->checkPermission(PermissionEnum::CREATE_STUDY_NOTE);

        $data = $request->validated();
        $data['created_by'] = auth()->id();

        $studyNote = StudyNote::create($data);

        return ResponseHelper::jsonResponse(
            new StudyNoteResource($studyNote->load([
                'student',
                'schoolDay',
                'subject',
            ])),
            __('messages.study_note.created'),
            ResponseAlias::HTTP_CREATED,
        );
    }

    /**
     * @throws PermissionException
     */
    public function showStudyNote($id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_STUDY_NOTE);

        $studyNote = StudyNote::with([
            'student',
            'schoolDay',
            'subject',
        ])
            ->findOrFail($id);

        return ResponseHelper::jsonResponse(
            new StudyNoteResource($studyNote),
            __('messages.study_note.fetched')
        );
    }

    /**
     * @throws PermissionException
     */
    public function updateStudyNote($request, $id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::UPDATE_STUDY_NOTE);

        $studyNote = StudyNote::findOrFail($id);
        $data = $request->validated();

        $studyNote->update($data);

        return ResponseHelper::jsonResponse(
            new StudyNoteResource($studyNote->load([
                'student',
                'schoolDay',
                'subject',
            ])),
            __('messages.study_note.updated')
        );
    }

    /**
     * @throws PermissionException
     */
    public function deleteStudyNote($id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::DELETE_STUDY_NOTE);

        $studyNote = StudyNote::findOrFail($id);
        $studyNote->delete();

        return ResponseHelper::jsonResponse(
            null,
            __('messages.study_note.deleted')
        );
    }

    /**
     * @throws PermissionException
     */
    public function restoreStudyNote($id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::MANAGE_DELETED_STUDY_NOTES);

        $studyNote = StudyNote::onlyTrashed()->findOrFail($id);
        $studyNote->restore();

        return ResponseHelper::jsonResponse(
            new StudyNoteResource($studyNote->load([
                'student',
                'schoolDay',
                'subject',
            ])),
            __('messages.study_note.restored')
        );
    }

    /**
     * @throws PermissionException
     */
    public function forceDeleteStudyNote($id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::MANAGE_DELETED_STUDY_NOTES);

//        $studyNote = StudyNote::onlyTrashed()->findOrFail($id);
        $studyNote = StudyNote::findOrFail($id);
        $studyNote->forceDelete();

        return ResponseHelper::jsonResponse(
            null,
            __('messages.study_note.force_deleted')
        );
    }

    /**
     * @throws PermissionException
     */
    public function getByStudent($studentId): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_STUDY_NOTES);

        $studyNotes = StudyNote::where('student_id', $studentId)
            ->with([
                'student',
                'schoolDay',
                'subject',
            ])
            ->orderBy('id', 'desc')
            ->paginate(15);

        return ResponseHelper::jsonResponse(
            StudyNoteResource::collection($studyNotes),
            __('messages.study_note.listed')
        );
    }

    /**
     * @throws PermissionException
     */
    public function getBySchoolDay($schoolDayId): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_STUDY_NOTES);

        $studyNotes = StudyNote::where('school_day_id', $schoolDayId)
            ->with([
                'student',
                'schoolDay',
                'subject',
            ])
            ->orderBy('id', 'desc')
            ->paginate(15);

        return ResponseHelper::jsonResponse(
            StudyNoteResource::collection($studyNotes),
            __('messages.study_note.listed')
        );
    }

    /**
     * @throws PermissionException
     */
    public function getBySubject($subjectId): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_STUDY_NOTES);

        $studyNotes = StudyNote::where('subject_id', $subjectId)
            ->with([
                'student',
                'schoolDay',
                'subject',
            ])
            ->orderBy('id', 'desc')
            ->paginate(15);

        return ResponseHelper::jsonResponse(
            StudyNoteResource::collection($studyNotes),
            __('messages.study_note.listed')
        );
    }
}
