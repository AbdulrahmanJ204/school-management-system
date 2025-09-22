<?php

namespace App\Services;

use App\Enums\Permissions\ExamPermission;
use App\Enums\ExamType;
use App\Exceptions\PermissionException;
use App\Helpers\ResponseHelper;
use App\Http\Resources\ExamResource;
use App\Models\Exam;
use App\Models\Subject;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ExamService
{
    

    /**
     * @throws PermissionException
     */
    public function listExams($request = null): JsonResponse
    {
        AuthHelper::authorize(ExamPermission::VIEW_EXAMS);

        $query = Exam::with([
             'schoolDay',
             'grade',
             'subject',
        ]);

        // Apply filters if provided
        if ($request) {
            if ($request->has('grade_id') && $request->grade_id) {
                $query->where('grade_id', $request->grade_id);
            }

            if ($request->has('school_day_id') && $request->school_day_id) {
                $query->where('school_day_id', $request->school_day_id);
            }

            if ($request->has('type') && $request->type) {
                $query->where('type', $request->type);
            }

            if ($request->has('subject_id') && $request->subject_id) {
                $query->where('subject_id', $request->subject_id);
            }
        }

        $exams = $query->orderBy('id', 'desc')->get();

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
        AuthHelper::authorize(ExamPermission::MANAGE_DELETED_EXAMS);

        $exams = Exam::onlyTrashed()
            ->with([
                 'schoolDay',
                 'grade',
                 'subject',
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
        AuthHelper::authorize(ExamPermission::CREATE_EXAM);

        $exam = Exam::create([
            'school_day_id' => $request->school_day_id,
            'grade_id' => Subject::findOrFail($request->subject_id)->getGrade()->id,
            'subject_id' => $request->subject_id,
            'type' => $request->type ?? ExamType::EXAM,
            'created_by' => Auth::user()->id,
        ]);

         $exam->load(['schoolDay', 'grade', 'subject']);

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
        AuthHelper::authorize(ExamPermission::VIEW_EXAM);

        $exam = Exam::with([
             'schoolDay',
             'grade',
             'subject',
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
        AuthHelper::authorize(ExamPermission::UPDATE_EXAM);

        $exam = Exam::findOrFail($id);

        $exam->update([
            'school_day_id' => $request->school_day_id,
            'grade_id' => Subject::findOrFail($request->subject_id)->getGrade()->id,
            'subject_id' => $request->subject_id,
            'type' => $request->type ?? ExamType::EXAM,
        ]);

         $exam->load(['schoolDay', 'grade', 'subject']);

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
        AuthHelper::authorize(ExamPermission::DELETE_EXAM);

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
        AuthHelper::authorize(ExamPermission::MANAGE_DELETED_EXAMS);

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
        AuthHelper::authorize(ExamPermission::MANAGE_DELETED_EXAMS);

//        $exam = Exam::onlyTrashed()->findOrFail($id);
        $exam = Exam::findOrFail($id);
        $exam->forceDelete();

        return ResponseHelper::jsonResponse(
            null,
            __('messages.exam.force_deleted')
        );
    }


}
