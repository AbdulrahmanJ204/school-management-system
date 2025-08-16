<?php

namespace App\Services;

use App\Enums\PermissionEnum;
use App\Helpers\ResponseHelper;
use App\Http\Requests\StudentEnrollmentRequest;
use App\Http\Resources\StudentEnrollmentResource;
use App\Models\StudentEnrollment;
use App\Models\Student;
use App\Models\Section;
use App\Models\Semester;
use App\Exceptions\PermissionException;
use App\Traits\HasPermissionChecks;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class StudentEnrollmentService
{
    use HasPermissionChecks;

    /**
     * Get list of all student enrollments.
     * @throws PermissionException
     */
    public function listStudentEnrollments(): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_STUDENT_ENROLLMENTS);

        $enrollments = StudentEnrollment::with([
            'year',
//            'student.user',
//            'section.grade',
//            'semester.year',
        ])->orderBy('created_at', 'desc')->get();

        return ResponseHelper::jsonResponse(
            StudentEnrollmentResource::collection($enrollments)
        );
    }

    /**
     * Create a new student enrollment.
     * @throws PermissionException
     */
    public function createStudentEnrollment(StudentEnrollmentRequest $request): JsonResponse
    {
        $this->checkPermission(PermissionEnum::CREATE_STUDENT_ENROLLMENT);

        $credentials = $request->validated();
        $section = Section::findOrFail($credentials['section_id']);
        $semester = Semester::findOrFail($credentials['semester_id']);
        
        $credentials['grade_id'] = $section->grade_id;
        $credentials['year_id'] = $semester->year_id;
        $credentials['created_by'] = auth()->id();

        // Check if enrollment already exists for this student and semester
        $existingEnrollment = StudentEnrollment::where('student_id', $credentials['student_id'])
            ->where('semester_id', $credentials['semester_id'])
            ->first();

        if ($existingEnrollment) {
            return ResponseHelper::jsonResponse(
                null,
                __('messages.student_enrollment.already_exists'),
                ResponseAlias::HTTP_BAD_REQUEST,
                false
            );
        }

        $enrollment = StudentEnrollment::create($credentials);
        $enrollment->load([
            'year',
//            'student.user',
//            'section.grade',
//            'semester.year',
        ]);

        return ResponseHelper::jsonResponse(
            new StudentEnrollmentResource($enrollment),
            __('messages.student_enrollment.created'),
            ResponseAlias::HTTP_CREATED,
            true
        );
    }

    /**
     * Show a specific student enrollment.
     * @throws PermissionException
     */
    public function showStudentEnrollment(StudentEnrollment $studentEnrollment): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_STUDENT_ENROLLMENT);

        $studentEnrollment->load([
            'year',
//            'student.user',
//            'section.grade',
//            'semester.year',
//            'studentMarks.subject.mainSubject'
        ]);

        return ResponseHelper::jsonResponse(
            new StudentEnrollmentResource($studentEnrollment)
        );
    }

    /**
     * Update a student enrollment.
     * @throws PermissionException
     */
    public function updateStudentEnrollment(StudentEnrollmentRequest $request, StudentEnrollment $studentEnrollment): JsonResponse
    {
        $this->checkPermission(PermissionEnum::UPDATE_STUDENT_ENROLLMENT);

        $credentials = $request->validated();

        $section = Section::findOrFail($credentials['section_id']);
        $semester = Semester::findOrFail($credentials['semester_id']);
        
        $credentials['grade_id'] = $section->grade_id;
        $credentials['year_id'] = $semester->year_id;

        // Check if enrollment already exists for this student and semester (excluding current enrollment)
        $existingEnrollment = StudentEnrollment::where('student_id', $credentials['student_id'])
            ->where('semester_id', $credentials['semester_id'])
            ->where('id', '!=', $studentEnrollment->id)
            ->first();

        if ($existingEnrollment) {
            return ResponseHelper::jsonResponse(
                null,
                __('messages.student_enrollment.already_exists'),
                ResponseAlias::HTTP_BAD_REQUEST,
                false
            );
        }

        $studentEnrollment->update($credentials);
        $studentEnrollment->load([
            'year',
//            'student.user',
//            'section.grade',
//            'semester.year',
        ]);

        return ResponseHelper::jsonResponse(
            new StudentEnrollmentResource($studentEnrollment),
            __('messages.student_enrollment.updated')
        );
    }

    /**
     * Delete a student enrollment.
     * @throws PermissionException
     */
    public function destroyStudentEnrollment(StudentEnrollment $studentEnrollment): JsonResponse
    {
        $this->checkPermission(PermissionEnum::DELETE_STUDENT_ENROLLMENT);

        if ($studentEnrollment->studentMarks()->exists()) {
            return ResponseHelper::jsonResponse(
                null,
                __('messages.student_enrollment.has_marks'),
                ResponseAlias::HTTP_BAD_REQUEST,
                false
            );
        }

        $studentEnrollment->delete();

        return ResponseHelper::jsonResponse(
            null,
            __('messages.student_enrollment.deleted')
        );
    }

    /**
     * Get list of trashed student enrollments.
     * @throws PermissionException
     */
    public function listTrashedStudentEnrollments(): JsonResponse
    {
        $this->checkPermission(PermissionEnum::MANAGE_DELETED_STUDENT_ENROLLMENTS);

        $enrollments = StudentEnrollment::with([
            'year',
//            'student.user',
//            'section.grade',
//            'semester.year',
        ])->onlyTrashed()->orderBy('created_at', 'desc')->get();

        return ResponseHelper::jsonResponse(
            StudentEnrollmentResource::collection($enrollments)
        );
    }

    /**
     * Restore a trashed student enrollment.
     * @throws PermissionException
     */
    public function restoreStudentEnrollment($id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::MANAGE_DELETED_STUDENT_ENROLLMENTS);

//        $enrollment = StudentEnrollment::withTrashed()->findOrFail($id);
        $enrollment = StudentEnrollment::findOrFail($id);

        if (!$enrollment->trashed()) {
            return ResponseHelper::jsonResponse(
                null,
                'Student enrollment is not deleted',
                ResponseAlias::HTTP_BAD_REQUEST,
                false
            );
        }

        $enrollment->restore();
        $enrollment->load([
            'year',
//            'student.user',
//            'section.grade',
//            'semester.year',
        ]);

        return ResponseHelper::jsonResponse(
            new StudentEnrollmentResource($enrollment),
            __('messages.student_enrollment.restored')
        );
    }

    /**
     * Force delete a trashed student enrollment.
     * @throws PermissionException
     */
    public function forceDeleteStudentEnrollment($id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::MANAGE_DELETED_STUDENT_ENROLLMENTS);

        $enrollment = StudentEnrollment::withTrashed()->findOrFail($id);

        if ($enrollment->studentMarks()->exists()) {
            return ResponseHelper::jsonResponse(
                null,
                __('messages.student_enrollment.has_marks'),
                ResponseAlias::HTTP_BAD_REQUEST,
                false
            );
        }

        $enrollment->forceDelete();

        return ResponseHelper::jsonResponse(
            null,
            __('messages.student_enrollment.force_deleted')
        );
    }

    /**
     * Get enrollments by student.
     * @throws PermissionException
     */
    public function getEnrollmentsByStudent($studentId): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_STUDENT_ENROLLMENTS);

        $student = Student::findOrFail($studentId);
        $enrollments = StudentEnrollment::where('student_id', $studentId)->with([
            'year',
//            'student.user',
//            'section.grade',
//            'semester.year',
        ])->orderBy('created_at', 'desc')->get();

        return ResponseHelper::jsonResponse(
            StudentEnrollmentResource::collection($enrollments)
        );
    }

    /**
     * Get enrollments by section.
     * @throws PermissionException
     */
    public function getEnrollmentsBySection($sectionId): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_STUDENT_ENROLLMENTS);

        $enrollments = StudentEnrollment::where('section_id', $sectionId)->with([
            'year',
//            'student.user',
//            'section.grade',
//            'semester.year',
        ])->orderBy('created_at', 'desc')->get();

        return ResponseHelper::jsonResponse(
            StudentEnrollmentResource::collection($enrollments)
        );
    }

    /**
     * Get enrollments by semester.
     * @throws PermissionException
     */
    public function getEnrollmentsBySemester($semesterId): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_STUDENT_ENROLLMENTS);

        $enrollments = StudentEnrollment::where('semester_id', $semesterId)->with([
            'year',
//            'student.user',
//            'section.grade',
//            'semester.year',
        ])->orderBy('created_at', 'desc')->get();

        return ResponseHelper::jsonResponse(
            StudentEnrollmentResource::collection($enrollments)
        );
    }
}
