<?php

namespace App\Services;

use App\Helpers\ResponseHelper;
use App\Http\Resources\StudentMarkResource;
use App\Models\StudentMark;
use App\Models\StudentEnrollment;
use App\Models\Subject;
use App\Exceptions\PermissionException;
use Illuminate\Http\Response;

class StudentMarkService
{
    /**
     * Get list of all student marks.
     */
    public function listStudentMarks()
    {
        $user = auth()->user();

        if (!$user->hasPermissionTo('عرض درجات الطلاب')) {
            throw new PermissionException();
        }

        $studentMarks = StudentMark::with([
            'subject.mainSubject.grade',
            'enrollment.student',
            'enrollment.section',
            'enrollment.semester',
            'createdBy'
        ])->orderBy('created_at', 'desc')->get();

        return ResponseHelper::jsonResponse(
            StudentMarkResource::collection($studentMarks)
        );
    }

    /**
     * Create a new student mark.
     */
    public function createStudentMark($request)
    {
        $user = auth()->user();

        if (!$user->hasPermissionTo('انشاء درجة طالب')) {
            throw new PermissionException();
        }

        $credentials = $request->validated();
        $credentials['created_by'] = $user->id;

        // Check if mark already exists for this enrollment and subject
        $existingMark = StudentMark::where('enrollment_id', $credentials['enrollment_id'])
            ->where('subject_id', $credentials['subject_id'])
            ->first();

        if ($existingMark) {
            return ResponseHelper::jsonResponse(
                null,
                __('messages.student_mark.already_exists'),
                400,
                false
            );
        }

        // Calculate total mark
        $subject = Subject::find($credentials['subject_id']);
        $total = $this->calculateTotalMark($credentials, $subject);
        $credentials['total'] = $total;

        $studentMark = StudentMark::create($credentials);
        $studentMark->load([
            'subject.mainSubject.grade',
            'enrollment.student',
            'enrollment.section',
            'enrollment.semester',
            'createdBy'
        ]);

        return ResponseHelper::jsonResponse(
            new StudentMarkResource($studentMark),
            __('messages.student_mark.created'),
            201,
            true
        );
    }

    /**
     * Show a specific student mark.
     */
    public function showStudentMark(StudentMark $studentMark)
    {
        $user = auth()->user();

        if (!$user->hasPermissionTo('عرض درجة طالب')) {
            throw new PermissionException();
        }

        $studentMark->load([
            'subject.mainSubject.grade',
            'enrollment.student',
            'enrollment.section',
            'enrollment.semester',
            'createdBy'
        ]);

        return ResponseHelper::jsonResponse(
            new StudentMarkResource($studentMark)
        );
    }

    /**
     * Update a student mark.
     */
    public function updateStudentMark($request, StudentMark $studentMark)
    {
        $user = auth()->user();

        if (!$user->hasPermissionTo('تعديل درجة طالب')) {
            throw new PermissionException();
        }

        $credentials = $request->validated();

        // Calculate total mark
        $subject = Subject::find($credentials['subject_id']);
        $total = $this->calculateTotalMark($credentials, $subject);
        $credentials['total'] = $total;

        $studentMark->update($credentials);
        $studentMark->load([
            'subject.mainSubject.grade',
            'enrollment.student',
            'enrollment.section',
            'enrollment.semester',
            'createdBy'
        ]);

        return ResponseHelper::jsonResponse(
            new StudentMarkResource($studentMark),
            __('messages.student_mark.updated')
        );
    }

    /**
     * Delete a student mark.
     */
    public function destroyStudentMark(StudentMark $studentMark)
    {
        $user = auth()->user();

        if (!$user->hasPermissionTo('حذف درجة طالب')) {
            throw new PermissionException();
        }

        $studentMark->delete();

        return ResponseHelper::jsonResponse(
            null,
            __('messages.student_mark.deleted')
        );
    }

    /**
     * Get student marks by enrollment.
     */
    public function getMarksByEnrollment($enrollmentId)
    {
        $user = auth()->user();

        if (!$user->hasPermissionTo('عرض درجات الطلاب')) {
            throw new PermissionException();
        }

        $enrollment = StudentEnrollment::findOrFail($enrollmentId);
        
        $studentMarks = StudentMark::where('enrollment_id', $enrollmentId)
            ->with([
                'subject.mainSubject.grade',
                'enrollment.student',
                'enrollment.section',
                'enrollment.semester',
                'createdBy'
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        return ResponseHelper::jsonResponse(
            StudentMarkResource::collection($studentMarks)
        );
    }

    /**
     * Get student marks by subject.
     */
    public function getMarksBySubject($subjectId)
    {
        $user = auth()->user();

        if (!$user->hasPermissionTo('عرض درجات الطلاب')) {
            throw new PermissionException();
        }

        $subject = Subject::findOrFail($subjectId);
        
        $studentMarks = StudentMark::where('subject_id', $subjectId)
            ->with([
                'subject.mainSubject.grade',
                'enrollment.student',
                'enrollment.section',
                'enrollment.semester',
                'createdBy'
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
    private function calculateTotalMark($credentials, $subject)
    {
        $total = 0;

        if (isset($credentials['homework']) && $credentials['homework']) {
            $total += ($credentials['homework'] * $subject->homework_percentage) / 100;
        }
        if (isset($credentials['oral']) && $credentials['oral']) {
            $total += ($credentials['oral'] * $subject->oral_percentage) / 100;
        }
        if (isset($credentials['activity']) && $credentials['activity']) {
            $total += ($credentials['activity'] * $subject->activity_percentage) / 100;
        }
        if (isset($credentials['quiz']) && $credentials['quiz']) {
            $total += ($credentials['quiz'] * $subject->quiz_percentage) / 100;
        }
        if (isset($credentials['exam']) && $credentials['exam']) {
            $total += ($credentials['exam'] * $subject->exam_percentage) / 100;
        }

        return round($total, 2);
    }
} 