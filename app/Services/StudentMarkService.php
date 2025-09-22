<?php

namespace App\Services;

use App\Enums\Permissions\StudentMarkPermission;
use App\Helpers\ResponseHelper;
use App\Http\Resources\StudentMarkResource;
use App\Models\StudentMark;
use App\Models\StudentEnrollment;
use App\Models\Subject;
use App\Models\Student;
use App\Models\User;
use App\Http\Requests\StudentMark\BulkStoreStudentMarkRequest;
use App\Http\Requests\StudentMark\BulkUpdateStudentMarkRequest;
use App\Exceptions\PermissionException;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Illuminate\Support\Facades\Auth;

class StudentMarkService
{
    

    /**
     * Get list of all student marks.
     * @throws PermissionException
     */
    public function listStudentMarks($request = null): JsonResponse
    {
        AuthHelper::authorize(StudentMarkPermission::VIEW_STUDENT_MARKS);

        // If no request or no subject_id, return existing marks as before
        if (!$request || !$request->has('subject_id')) {
            $query = StudentMark::with([
                'subject.mainSubject.grade',
                'enrollment',
                'enrollment.student',
                'enrollment.student.user',
                'enrollment.section',
                'enrollment.semester',
                'createdBy',
            ]);

            // Apply filters if request is provided
            if ($request) {
                // Filter by enrollment_id
                if ($request->has('enrollment_id')) {
                    $query->where('enrollment_id', $request->enrollment_id);
                }

                // Filter by semester_id
                if ($request->has('semester_id')) {
                    $query->whereHas('enrollment', function ($q) use ($request) {
                        $q->where('semester_id', $request->semester_id);
                    });
                }

                // Filter by section_id
                if ($request->has('section_id')) {
                    $query->whereHas('enrollment', function ($q) use ($request) {
                        $q->where('section_id', $request->section_id);
                    });
                }
            }

            $studentMarks = $query->orderBy('created_at', 'desc')->get();

            return ResponseHelper::jsonResponse(
                StudentMarkResource::collection($studentMarks)
            );
        }

        // Get the subject
        $subject = Subject::findOrFail($request->subject_id);
        
        // Build enrollment query
        $enrollmentQuery = StudentEnrollment::with([
            'student.user',
            'section',
            'semester',
            'studentMarks' => function ($query) use ($request) {
                $query->where('subject_id', $request->subject_id);
            }
        ]);

        // Apply filters
        if ($request->has('semester_id')) {
            $enrollmentQuery->where('semester_id', $request->semester_id);
        }

        if ($request->has('section_id')) {
            $enrollmentQuery->where('section_id', $request->section_id);
        }

        if ($request->has('enrollment_id')) {
            $enrollmentQuery->where('id', $request->enrollment_id);
        }

        $enrollments = $enrollmentQuery->get();
        $allStudentMarks = collect();

        foreach ($enrollments as $enrollment) {
            // Check if student already has a mark for this subject
            $existingMark = $enrollment->studentMarks->first();
            
            if ($existingMark) {
                // Student already has a mark, add it to the collection
                $allStudentMarks->push($existingMark);
            } else {
                // Student doesn't have a mark, create a default one
                $defaultMark = StudentMark::create([
                    'subject_id' => $request->subject_id,
                    'enrollment_id' => $enrollment->id,
                    'homework' => 0,
                    'oral' => 0,
                    'activity' => 0,
                    'quiz' => 0,
                    'exam' => 0,
                    'total' => 0,
                    'created_by' => Auth::user()->id
                ]);

                // Load the relationships for the new mark
                $defaultMark->load([
                    'subject.mainSubject.grade',
                    'enrollment.student.user',
                    'enrollment.section',
                    'enrollment.semester',
                ]);

                $allStudentMarks->push($defaultMark);
            }
        }

        // Sort by student name
        $sortedMarks = $allStudentMarks->sortBy(function ($mark) {
            return $mark->enrollment->student->user->first_name ?? '';
        })->values();

        return ResponseHelper::jsonResponse(
            StudentMarkResource::collection($sortedMarks)
        );
    }

    /**
     * Create a new student mark.
     * @throws PermissionException
     */
    public function createStudentMark($request): JsonResponse
    {
        AuthHelper::authorize(StudentMarkPermission::CREATE_STUDENT_MARK);

        $credentials = $request->validated();
        $credentials['created_by'] = Auth::user()->id;

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
            'enrollment.student.user',
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
        AuthHelper::authorize(StudentMarkPermission::VIEW_STUDENT_MARK);

        $studentMark->load([
            'subject.mainSubject.grade',
            'enrollment.student.user',
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
        AuthHelper::authorize(StudentMarkPermission::UPDATE_STUDENT_MARK);

        $credentials = $request->validated();

        $credentials['total'] = $this->calculateTotalMark($credentials);

        $studentMark->update($credentials);
        $studentMark->load([
            'subject.mainSubject.grade',
            'enrollment.student.user',
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
        AuthHelper::authorize(StudentMarkPermission::DELETE_STUDENT_MARK);

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
        AuthHelper::authorize(StudentMarkPermission::VIEW_STUDENT_MARKS);

        $enrollment = StudentEnrollment::findOrFail($enrollmentId);
        $studentMarks = StudentMark::where('enrollment_id', $enrollmentId)->with([
            'subject.mainSubject.grade',
            'enrollment.student.user',
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
        AuthHelper::authorize(StudentMarkPermission::VIEW_STUDENT_MARKS);

        $studentMarks = StudentMark::where('subject_id', $subjectId)->with([
            'subject.mainSubject.grade',
            'enrollment.student.user',
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
        AuthHelper::authorize(StudentMarkPermission::VIEW_STUDENT_MARKS);

        $subject = Subject::findOrFail($subjectId);
        $studentMarks = StudentMark::where('subject_id', $subjectId)
            ->whereHas('enrollment', function ($query) use ($sectionId) {
                $query->where('section_id', $sectionId);
            })
            ->with([
                'subject.mainSubject.grade',
                'enrollment.student.user',
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
            $user = Auth::user();

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
            $user = Auth::user();

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

    /**
     * Create multiple student marks in bulk.
     * @throws PermissionException
     */
    public function bulkCreateStudentMarks(BulkStoreStudentMarkRequest $request): JsonResponse
    {
        AuthHelper::authorize(StudentMarkPermission::CREATE_STUDENT_MARK);

        try {
            $marks = $request->validated()['marks'];
            $createdMarks = [];
            $errors = [];

            foreach ($marks as $index => $markData) {
                try {
                    // Check if mark already exists for this enrollment and subject
                    $existingMark = StudentMark::where('enrollment_id', $markData['enrollment_id'])
                        ->where('subject_id', $markData['subject_id'])
                        ->first();

                    if ($existingMark) {
                        $errors[] = [
                            'index' => $index,
                            'error' => 'يوجد درجة مسجلة مسبقاً لهذا الطالب في هذه المادة'
                        ];
                        continue;
                    }

                    $studentMark = StudentMark::create([
                        'subject_id' => $markData['subject_id'],
                        'enrollment_id' => $markData['enrollment_id'],
                        'homework' => $markData['homework'] ?? null,
                        'oral' => $markData['oral'] ?? null,
                        'activity' => $markData['activity'] ?? null,
                        'quiz' => $markData['quiz'] ?? null,
                        'exam' => $markData['exam'] ?? null,
                        'created_by' => Auth::user()->id,
                    ]);

                    $createdMarks[] = new StudentMarkResource($studentMark->load([
                        'subject',
                        'enrollment.student.user',
                        'enrollment.section',
                        'createdBy'
                    ]));

                } catch (\Exception $e) {
                    $errors[] = [
                        'index' => $index,
                        'error' => 'فشل في إنشاء الدرجة: ' . $e->getMessage()
                    ];
                }
            }

            $responseData = [
                'created_marks' => $createdMarks,
                'total_created' => count($createdMarks),
                'total_requested' => count($marks),
            ];

            if (!empty($errors)) {
                $responseData['errors'] = $errors;
                $responseData['total_errors'] = count($errors);
            }

            $message = count($createdMarks) > 0 
                ? 'تم إنشاء ' . count($createdMarks) . ' درجة بنجاح'
                : 'لم يتم إنشاء أي درجات';

            if (!empty($errors)) {
                $message .= ' مع ' . count($errors) . ' خطأ';
            }

            return ResponseHelper::jsonResponse(
                $responseData,
                $message,
                count($createdMarks) > 0 ? 201 : 422,
                count($createdMarks) > 0
            );

        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(
                null,
                'حدث خطأ في إنشاء الدرجات: ' . $e->getMessage(),
                500,
                false
            );
        }
    }

    /**
     * Update multiple student marks in bulk.
     * @throws PermissionException
     */
    public function bulkUpdateStudentMarks(BulkUpdateStudentMarkRequest $request): JsonResponse
    {
        AuthHelper::authorize(StudentMarkPermission::UPDATE_STUDENT_MARK);

        try {
            $marks = $request->validated()['marks'];
            $updatedMarks = [];
            $errors = [];

            foreach ($marks as $index => $markData) {
                try {
                    $studentMark = StudentMark::find($markData['id']);

                    if (!$studentMark) {
                        $errors[] = [
                            'index' => $index,
                            'error' => 'الدرجة المحددة غير موجودة'
                        ];
                        continue;
                    }

                    $studentMark->update([
                        'subject_id' => $markData['subject_id'],
                        'enrollment_id' => $markData['enrollment_id'],
                        'homework' => $markData['homework'] ?? null,
                        'oral' => $markData['oral'] ?? null,
                        'activity' => $markData['activity'] ?? null,
                        'quiz' => $markData['quiz'] ?? null,
                        'exam' => $markData['exam'] ?? null,
                    ]);

                    $updatedMarks[] = new StudentMarkResource($studentMark->load([
                        'subject',
                        'enrollment.student.user',
                        'enrollment.section',
                        'createdBy'
                    ]));

                } catch (\Exception $e) {
                    $errors[] = [
                        'index' => $index,
                        'error' => 'فشل في تحديث الدرجة: ' . $e->getMessage()
                    ];
                }
            }

            $responseData = [
                'updated_marks' => $updatedMarks,
                'total_updated' => count($updatedMarks),
                'total_requested' => count($marks),
            ];

            if (!empty($errors)) {
                $responseData['errors'] = $errors;
                $responseData['total_errors'] = count($errors);
            }

            $message = count($updatedMarks) > 0 
                ? 'تم تحديث ' . count($updatedMarks) . ' درجة بنجاح'
                : 'لم يتم تحديث أي درجات';

            if (!empty($errors)) {
                $message .= ' مع ' . count($errors) . ' خطأ';
            }

            return ResponseHelper::jsonResponse(
                $responseData,
                $message,
                count($updatedMarks) > 0 ? 200 : 422,
                count($updatedMarks) > 0
            );

        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(
                null,
                'حدث خطأ في تحديث الدرجات: ' . $e->getMessage(),
                500,
                false
            );
        }
    }
}
