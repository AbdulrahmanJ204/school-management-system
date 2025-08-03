<?php

namespace App\Services;

use App\Enums\PermissionEnum;
use App\Helpers\ResponseHelper;
use App\Http\Resources\StudentMarkResource;
use App\Models\StudentMark;
use App\Models\StudentEnrollment;
use App\Models\Subject;
use App\Exceptions\PermissionException;
use App\Traits\HasPermissionChecks;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class StudentMarkService
{
    use HasPermissionChecks;

    /**
     * Get list of all student marks.
     * @throws PermissionException
     */
    public function listStudentMarks(): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_STUDENT_MARKS);

        $studentMarks = StudentMark::with([
//            'subject.mainSubject.grade',
//            'enrollment.student',
//            'enrollment.section',
//            'enrollment.semester',
        ])->orderBy('created_at', 'desc')->get();

        return ResponseHelper::jsonResponse(
            StudentMarkResource::collection($studentMarks)
        );
    }

    /**
     * Create a new student mark.
     * @throws PermissionException
     */
    public function createStudentMark($request): JsonResponse
    {
        $this->checkPermission(PermissionEnum::CREATE_STUDENT_MARK);

        $credentials = $request->validated();
        $credentials['created_by'] = auth()->id();

        // Check if mark already exists for this enrollment and subject
        $existingMark = StudentMark::where('enrollment_id', $credentials['enrollment_id'])
            ->where('subject_id', $credentials['subject_id'])
            ->first();

        if ($existingMark) {
            return ResponseHelper::jsonResponse(
                null,
                __('messages.student_mark.already_exists'),
                ResponseAlias::HTTP_BAD_REQUEST,
                false
            );
        }

        $credentials['total'] = $this->calculateTotalMark($credentials);

        $studentMark = StudentMark::create($credentials);
        $studentMark->load([
//            'subject.mainSubject.grade',
//            'enrollment.student',
//            'enrollment.section',
//            'enrollment.semester',
        ]);

        return ResponseHelper::jsonResponse(
            new StudentMarkResource($studentMark),
            __('messages.student_mark.created'),
            ResponseAlias::HTTP_CREATED,
            true
        );
    }

    /**
     * Show a specific student mark.
     * @throws PermissionException
     */
    public function showStudentMark(StudentMark $studentMark): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_STUDENT_MARK);

        $studentMark->load([
//            'subject.mainSubject.grade',
//            'enrollment.student',
//            'enrollment.section',
//            'enrollment.semester',
        ]);

        return ResponseHelper::jsonResponse(
            new StudentMarkResource($studentMark)
        );
    }

    /**
     * Update a student mark.
     * @throws PermissionException
     */
    public function updateStudentMark($request, StudentMark $studentMark): JsonResponse
    {
        $this->checkPermission(PermissionEnum::UPDATE_STUDENT_MARK);

        $credentials = $request->validated();

        $credentials['total'] = $this->calculateTotalMark($credentials);

        $studentMark->update($credentials);
        $studentMark->load([
//            'subject.mainSubject.grade',
//            'enrollment.student',
//            'enrollment.section',
//            'enrollment.semester',
        ]);

        return ResponseHelper::jsonResponse(
            new StudentMarkResource($studentMark),
            __('messages.student_mark.updated')
        );
    }

    /**
     * Delete a student mark.
     * @throws PermissionException
     */
    public function destroyStudentMark(StudentMark $studentMark): JsonResponse
    {
        $this->checkPermission(PermissionEnum::DELETE_STUDENT_MARK);

        $studentMark->delete();

        return ResponseHelper::jsonResponse(
            null,
            __('messages.student_mark.deleted')
        );
    }

    /**
     * Get marks by enrollment.
     * @throws PermissionException
     */
    public function getMarksByEnrollment($enrollmentId): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_STUDENT_MARKS);

        $enrollment = StudentEnrollment::findOrFail($enrollmentId);
        $studentMarks = StudentMark::where('enrollment_id', $enrollmentId)->with([
//            'subject.mainSubject.grade',
//            'enrollment.student',
//            'enrollment.section',
//            'enrollment.semester',
        ])->orderBy('created_at', 'desc')->get();

        return ResponseHelper::jsonResponse(
            StudentMarkResource::collection($studentMarks)
        );
    }

    /**
     * Get marks by subject.
     * @throws PermissionException
     */
    public function getMarksBySubject($subjectId): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_STUDENT_MARKS);

        $studentMarks = StudentMark::where('subject_id', $subjectId)->with([
//            'subject.mainSubject.grade',
//            'enrollment.student',
//            'enrollment.section',
//            'enrollment.semester',
        ])->orderBy('created_at', 'desc')->get();

        return ResponseHelper::jsonResponse(
            StudentMarkResource::collection($studentMarks)
        );
    }

    /**
     * Get marks by subject and section.
     * @throws PermissionException
     */
    public function getMarksBySubjectAndSection($subjectId, $sectionId): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_STUDENT_MARKS);

        $subject = Subject::findOrFail($subjectId);
        $studentMarks = StudentMark::where('subject_id', $subjectId)
            ->whereHas('enrollment', function ($query) use ($sectionId) {
                $query->where('section_id', $sectionId);
            })
            ->with([
//                'subject.mainSubject.grade',
//                'enrollment.student',
//                'enrollment.section',
//                'enrollment.semester',
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        return ResponseHelper::jsonResponse(
            StudentMarkResource::collection($studentMarks)
        );
    }

    /**
     * Calculate total mark based on subject percentages.
     */
    private function calculateTotalMark($credentials)
    {
        $total = $credentials['homework'] + $credentials['oral'] + $credentials['activity']
            + $credentials['quiz'] + $credentials['exam'];

        return $total;
    }
}
