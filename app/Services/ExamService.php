<?php

namespace App\Services;

use App\Enums\PermissionEnum;
use App\Exceptions\PermissionException;
use App\Helpers\ResponseHelper;
use App\Http\Resources\ExamResource;
use App\Models\Exam;
use App\Models\MainSubject;
use App\Models\Subject;
use App\Traits\HasPermissionChecks;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ExamService
{
    use HasPermissionChecks;

    /**
     * @throws PermissionException
     */
    public function listExams(): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_EXAMS);

        $exams = Exam::with([
            // 'schoolDay',
            // 'grade',
            // 'mainSubject',
        ])
            ->orderBy('id', 'desc')
            ->get();

        return ResponseHelper::jsonResponse(
            ExamResource::collection($exams),
            __('messages.exam.listed')
        );
    }

    /**
     * @throws PermissionException
     */
    public function listTrashedExams(): JsonResponse
    {
        $this->checkPermission(PermissionEnum::MANAGE_DELETED_EXAMS);

        $exams = Exam::onlyTrashed()
            ->with([
                // 'schoolDay',
                // 'grade',
                // 'mainSubject',
            ])
            ->orderBy('id', 'desc')
            ->get();

        return ResponseHelper::jsonResponse(
            ExamResource::collection($exams),
            __('messages.exam.listed')
        );
    }

    /**
     * @throws PermissionException
     */
    public function createExam($request): JsonResponse
    {
        $this->checkPermission(PermissionEnum::CREATE_EXAM);

        $exam = Exam::create([
            'school_day_id' => $request->school_day_id,
            'grade_id' => MainSubject::findOrFail($request->main_subject_id)->grade_id,
            'main_subject_id' => $request->main_subject_id,
            'created_by' => auth()->id(),
        ]);

        // $exam->load(['schoolDay', 'grade', 'subject']);

        return ResponseHelper::jsonResponse(
            new ExamResource($exam),
            __('messages.exam.created'),
            ResponseAlias::HTTP_CREATED
        );
    }

    /**
     * @throws PermissionException
     */
    public function showExam($id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_EXAM);

        $exam = Exam::with([
            // 'schoolDay',
            // 'grade',
            // 'mainSubject',
        ])->findOrFail($id);

        return ResponseHelper::jsonResponse(
            new ExamResource($exam),
            __('messages.exam.showed')
        );
    }

    /**
     * @throws PermissionException
     */
    public function updateExam($request, $id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::UPDATE_EXAM);

        $exam = Exam::findOrFail($id);

        $exam->update([
            'school_day_id' => $request->school_day_id,
            'grade_id' => MainSubject::findOrFail($request->main_subject_id)->grade_id,
            'main_subject_id' => $request->main_subject_id,
        ]);

        // $exam->load(['schoolDay', 'grade', 'subject']);

        return ResponseHelper::jsonResponse(
            new ExamResource($exam),
            __('messages.exam.updated')
        );
    }

    /**
     * @throws PermissionException
     */
    public function deleteExam($id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::DELETE_EXAM);

        $exam = Exam::findOrFail($id);
        $exam->delete();

        return ResponseHelper::jsonResponse(
            null,
            __('messages.exam.deleted')
        );
    }

    /**
     * @throws PermissionException
     */
    public function restoreExam($id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::MANAGE_DELETED_EXAMS);

        $exam = Exam::onlyTrashed()->findOrFail($id);
        $exam->restore();

        return ResponseHelper::jsonResponse(
            new ExamResource($exam),
            __('messages.exam.restored')
        );
    }

    /**
     * @throws PermissionException
     */
    public function forceDeleteExam($id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::MANAGE_DELETED_EXAMS);

//        $exam = Exam::onlyTrashed()->findOrFail($id);
        $exam = Exam::findOrFail($id);
        $exam->forceDelete();

        return ResponseHelper::jsonResponse(
            null,
            __('messages.exam.force_deleted')
        );
    }

    /**
     * @throws PermissionException
     */
    public function getBySchoolDay($schoolDayId): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_EXAMS);

        $exams = Exam::with([
            // 'schoolDay',
            // 'grade',
            // 'mainSubject',
        ])
            ->where('school_day_id', $schoolDayId)
            ->orderBy('id', 'desc')
            ->get();

        return ResponseHelper::jsonResponse(
            ExamResource::collection($exams),
            __('messages.exam.listed')
        );
    }

    /**
     * @throws PermissionException
     */
    public function getByGrade($gradeId): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_EXAMS);

        $exams = Exam::with([
            // 'schoolDay',
            // 'grade',
            // 'mainSubject',
        ])
            ->where('grade_id', $gradeId)
            ->orderBy('id', 'desc')
            ->get();

        return ResponseHelper::jsonResponse(
            ExamResource::collection($exams),
            __('messages.exam.listed')
        );
    }
}
