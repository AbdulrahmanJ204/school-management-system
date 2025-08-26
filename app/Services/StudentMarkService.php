<?php

namespace App\Services;

use App\Enums\PermissionEnum;
use App\Helpers\ResponseHelper;
use App\Http\Resources\StudentMarkResource;
use App\Models\StudentMark;
use App\Models\StudentEnrollment;
use App\Models\Subject;
use App\Models\Student;
use App\Models\User;
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
            'subject.mainSubject.grade',
            'enrollment.student',
            'enrollment.section',
            'enrollment.semester',
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
            'subject.mainSubject.grade',
            'enrollment.student',
            'enrollment.section',
            'enrollment.semester',
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
            'subject.mainSubject.grade',
            'enrollment.student',
            'enrollment.section',
            'enrollment.semester',
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
            'subject.mainSubject.grade',
            'enrollment.student',
            'enrollment.section',
            'enrollment.semester',
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
            'subject.mainSubject.grade',
            'enrollment.student',
            'enrollment.section',
            'enrollment.semester',
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
            'subject.mainSubject.grade',
            'enrollment.student',
            'enrollment.section',
            'enrollment.semester',
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
                'subject.mainSubject.grade',
                'enrollment.student',
                'enrollment.section',
                'enrollment.semester',
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
        $subject = Subject::find($credentials['subject_id']);
        if (!$subject) {
            return 0;
        }

        $total = 0;

        if (isset($credentials['homework']) && $credentials['homework'] !== null) {
            $total += ($credentials['homework'] * $subject->homework_percentage) / 100;
        }
        if (isset($credentials['oral']) && $credentials['oral'] !== null) {
            $total += ($credentials['oral'] * $subject->oral_percentage) / 100;
        }
        if (isset($credentials['activity']) && $credentials['activity'] !== null) {
            $total += ($credentials['activity'] * $subject->activity_percentage) / 100;
        }
        if (isset($credentials['quiz']) && $credentials['quiz'] !== null) {
            $total += ($credentials['quiz'] * $subject->quiz_percentage) / 100;
        }
        if (isset($credentials['exam']) && $credentials['exam'] !== null) {
            $total += ($credentials['exam'] * $subject->exam_percentage) / 100;
        }

        return round($total, 2);
    }

    /**
     * Get authenticated student's quiz and exam marks for a specific semester
     *
     * @param int $semesterId
     * @return JsonResponse
     */
    public function getMyMarks(int $semesterId): JsonResponse
    {
        try {
            $user = auth()->user();

            $student = $user->student;
            if (!$student) {
                return ResponseHelper::jsonResponse(
                    null,
                    'لم يتم العثور على بيانات الطالب',
                    404,
                    false
                );
            }

            // Get student enrollment for the specified semester
            $enrollment = $student->studentEnrollments()
                ->where('semester_id', $semesterId)
                ->first();

            if (!$enrollment) {
                return ResponseHelper::jsonResponse(
                    null,
                    'لم يتم العثور على تسجيل للطالب في هذا الفصل الدراسي',
                    404,
                    false
                );
            }

            // Get student marks for this enrollment
            $studentMarks = StudentMark::where('enrollment_id', $enrollment->id)
                ->with(['subject'])
                ->get();

            $subjectsMarks = [];

            foreach ($studentMarks as $mark) {
                $subject = $mark->subject;

                // Calculate max and min marks for quiz
                if ($mark->quiz !== null) {
                    $quizMaxMark = ($subject->quiz_percentage / 100) * $subject->full_mark;
                    $quizMinMark = $quizMaxMark * ($subject->mainSubject->success_rate / 100);

                    $subjectsMarks[] = [
                        'id' => $mark->id,
                        'subject_name' => $subject->name,
                        'type' => 'مذاكرة',
                        'max_mark' => round($quizMaxMark),
                        'min_mark' => round($quizMinMark),
                        'student_mark' => $mark->quiz
                    ];
                }

                // Calculate max and min marks for exam
                if ($mark->exam !== null) {
                    $examMaxMark = ($subject->exam_percentage / 100) * $subject->full_mark;
                    $examMinMark = $examMaxMark * ($subject->mainSubject->success_rate / 100);

                    $subjectsMarks[] = [
                        'id' => $mark->id,
                        'subject_name' => $subject->name,
                        'type' => 'امتحان',
                        'max_mark' => round($examMaxMark),
                        'min_mark' => round($examMinMark),
                        'student_mark' => $mark->exam
                    ];
                }
            }

            return ResponseHelper::jsonResponse(
                ['subjects_marks' => $subjectsMarks],
                'تم جلب علامات الطالب بنجاح'
            );

        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(
                null,
                'حدث خطأ في جلب علامات الطالب: ' . $e->getMessage(),
                500,
                false
            );
        }
    }

    public function getMyAllMarks(): JsonResponse
    {
        try {
            $user = auth()->user();

            $student = $user->student;
            if (!$student) {
                return ResponseHelper::jsonResponse(
                    null,
                    'لم يتم العثور على بيانات الطالب',
                    404,
                    false
                );
            }

            // Get student enrollment for All semester
            $enrollments = $student->studentEnrollments()
                ->get();

            if (!$enrollments) {
                return ResponseHelper::jsonResponse(
                    null,
                    'لم يتم العثور على تسجيل للطالب',
                    404,
                    false
                );
            }

            // Get student marks for all enrollments
            $subjectsMarks = [];

            foreach ($enrollments as $enrollment) {
                $studentMarks = StudentMark::where('enrollment_id', $enrollment->id)
                    ->with(['subject', 'subject.mainSubject'])
                    ->get();

                foreach ($studentMarks as $mark) {
                    $subject = $mark->subject;

                    // Quiz
                    if ($mark->quiz !== null) {
                        $quizMaxMark = ($subject->quiz_percentage / 100) * $subject->full_mark;
                        $quizMinMark = $quizMaxMark * ($subject->mainSubject->success_rate / 100);

                        $subjectsMarks[] = [
                            'id' => $mark->id,
                            'subject_name' => $subject->name,
                            'type' => 'مذاكرة',
                            'max_mark' => round($quizMaxMark),
                            'min_mark' => round($quizMinMark),
                            'student_mark' => $mark->quiz
                        ];
                    }

                    // Exam
                    if ($mark->exam !== null) {
                        $examMaxMark = ($subject->exam_percentage / 100) * $subject->full_mark;
                        $examMinMark = $examMaxMark * ($subject->mainSubject->success_rate / 100);

                        $subjectsMarks[] = [
                            'id' => $mark->id,
                            'subject_name' => $subject->name,
                            'type' => 'امتحان',
                            'max_mark' => round($examMaxMark),
                            'min_mark' => round($examMinMark),
                            'student_mark' => $mark->exam
                        ];
                    }
                }
            }

            return ResponseHelper::jsonResponse(
                ['subjects_marks' => $subjectsMarks],
                'تم جلب علامات الطالب بنجاح'
            );

        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(
                null,
                'حدث خطأ في جلب علامات الطالب: ' . $e->getMessage(),
                500,
                false
            );
        }
    }
}
