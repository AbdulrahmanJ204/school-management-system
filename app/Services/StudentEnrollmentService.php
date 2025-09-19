<?php

namespace App\Services;

use App\Enums\Permissions\StudentEnrollmentPermission;
use App\Helpers\ResponseHelper;
use App\Http\Requests\StudentEnrollment\ListStudentEnrollmentRequest;
use App\Http\Requests\StudentEnrollment\StoreStudentEnrollmentRequest;
use App\Http\Requests\StudentEnrollment\UpdateStudentEnrollmentRequest;
use App\Http\Resources\StudentEnrollmentResource;
use App\Models\StudentEnrollment;
use App\Models\Section;
use App\Models\Semester;
use App\Exceptions\PermissionException;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class StudentEnrollmentService
{
    

    /**
     * @throws PermissionException
     */
    public function listStudentEnrollments(ListStudentEnrollmentRequest $request): JsonResponse
    {
        AuthHelper::authorize(StudentEnrollmentPermission::VIEW_STUDENT_ENROLLMENTS);

        $query = StudentEnrollment::with([
            'year',
            'student.user',
            'section.grade',
            'semester.year',
        ]);

        // filters
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        if ($request->filled('semester_id')) {
            $query->where('semester_id', $request->semester_id);
        }

        if ($request->filled('grade_id')) {
            $query->whereHas('section', function ($q) use ($request) {
                $q->where('grade_id', $request->grade_id);
            });
        }

        if ($request->filled('year_id')) {
            $query->where('year_id', $request->year_id);
        }

        $enrollments = $query->orderBy('created_at', 'desc')->get();

        return ResponseHelper::jsonResponse(
            StudentEnrollmentResource::collection($enrollments)
        );
    }

    /**
     * @throws PermissionException
     */
    public function createStudentEnrollment(StoreStudentEnrollmentRequest $request): JsonResponse
    {
        AuthHelper::authorize(StudentEnrollmentPermission::CREATE_STUDENT_ENROLLMENT);

        $credentials = $request->validated();
        
        // Handle section_id - it can be null
        if (isset($credentials['section_id']) && $credentials['section_id']) {
            $section = Section::findOrFail($credentials['section_id']);
            $credentials['grade_id'] = $section->grade_id;
        }
        
        $semester = Semester::findOrFail($credentials['semester_id']);

        $credentials['year_id'] = $semester->year_id;
        $credentials['created_by'] = Auth::user()->id;

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
            'student.user',
            'section.grade',
            'semester.year',
        ]);

        return ResponseHelper::jsonResponse(
            new StudentEnrollmentResource($enrollment),
            __('messages.student_enrollment.created'),
            ResponseAlias::HTTP_CREATED,
        );
    }

    /**
     * @throws PermissionException
     */
    public function showStudentEnrollment(StudentEnrollment $studentEnrollment): JsonResponse
    {
        AuthHelper::authorize(StudentEnrollmentPermission::VIEW_STUDENT_ENROLLMENT);

        $studentEnrollment->load([
            'year',
            'student.user',
            'section.grade',
            'semester.year',
            'studentMarks.subject.mainSubject'
        ]);

        return ResponseHelper::jsonResponse(
            new StudentEnrollmentResource($studentEnrollment)
        );
    }

    /**
     * @throws PermissionException
     */
    public function updateStudentEnrollment(UpdateStudentEnrollmentRequest $request, StudentEnrollment $studentEnrollment): JsonResponse
    {
        AuthHelper::authorize(StudentEnrollmentPermission::UPDATE_STUDENT_ENROLLMENT);

        $credentials = $request->validated();

        // Handle section_id - it can be null
        if (isset($credentials['section_id']) && $credentials['section_id']) {
            $section = Section::findOrFail($credentials['section_id']);
            $credentials['grade_id'] = $section->grade_id;
        }
        
        $semester = Semester::findOrFail($credentials['semester_id']);

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
            'student.user',
            'section.grade',
            'semester.year',
        ]);

        return ResponseHelper::jsonResponse(
            new StudentEnrollmentResource($studentEnrollment),
            __('messages.student_enrollment.updated')
        );
    }

    /**
     * @throws PermissionException
     */
    public function destroyStudentEnrollment(StudentEnrollment $studentEnrollment): JsonResponse
    {
        AuthHelper::authorize(StudentEnrollmentPermission::DELETE_STUDENT_ENROLLMENT);

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
     * @throws PermissionException
     */
    public function listTrashedStudentEnrollments(): JsonResponse
    {
        AuthHelper::authorize(StudentEnrollmentPermission::MANAGE_DELETED_STUDENT_ENROLLMENTS);

        $enrollments = StudentEnrollment::with([
            'year',
            'student.user',
            'section.grade',
            'semester.year',
        ])->onlyTrashed()->orderBy('created_at', 'desc')->get();

        return ResponseHelper::jsonResponse(
            StudentEnrollmentResource::collection($enrollments)
        );
    }

    /**
     * @throws PermissionException
     */
    public function restoreStudentEnrollment($id): JsonResponse
    {
        AuthHelper::authorize(StudentEnrollmentPermission::MANAGE_DELETED_STUDENT_ENROLLMENTS);

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
            'student.user',
            'section.grade',
            'semester.year',
        ]);

        return ResponseHelper::jsonResponse(
            new StudentEnrollmentResource($enrollment),
            __('messages.student_enrollment.restored')
        );
    }

    /**
     * @throws PermissionException
     */
    public function forceDeleteStudentEnrollment($id): JsonResponse
    {
        AuthHelper::authorize(StudentEnrollmentPermission::MANAGE_DELETED_STUDENT_ENROLLMENTS);

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
}
