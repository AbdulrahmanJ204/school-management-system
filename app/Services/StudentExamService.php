<?php

namespace App\Services;

use App\Enums\ExamType;
use App\Helpers\ResponseHelper;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\User;
use App\Models\Exam;
use App\Models\Quiz;
use App\Models\QuizTarget;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class StudentExamService
{
    /**
     * Get all exams and quizzes for student
     *
     * @param int $userId
     * @return JsonResponse
     */
    public function getStudentExams(int $userId): JsonResponse
    {
        try {
            // Verify user is student
            $user = User::with('student')->findOrFail($userId);
            if (!$user->student) {
                throw new Exception('الطالب غير موجود');
            }

            // Get current enrollment
            $currentEnrollment = $this->getCurrentEnrollment($user->student);
            
            if (!$currentEnrollment) {
                throw new Exception('الطالب ليس مسجل في أي شعبة');
            }

            // Get exams and quizzes
            $examsList = $this->getStudentExamsAndQuizzes($currentEnrollment);

            return ResponseHelper::jsonResponse(
                $examsList,
                'تم جلب الامتحانات بنجاح'
            );

        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(
                null,
                'حدث خطأ في جلب الامتحانات: ' . $e->getMessage(),
                Response::HTTP_BAD_REQUEST,
                false
            );
        }
    }

    /**
     * Get current student enrollment
     *
     * @param Student $student
     * @return StudentEnrollment|null
     */
    private function getCurrentEnrollment(Student $student): ?StudentEnrollment
    {
        return $student->studentEnrollments()
            ->with(['section.grade', 'semester.year'])
            ->whereHas('semester.year', function ($query) {
                $query->where('is_active', true);
            })
            ->whereHas('semester', function ($query) {
                $query->where('is_active', true);
            })
            ->first();
    }

    /**
     * Get student exams and quizzes
     *
     * @param StudentEnrollment $enrollment
     * @return array
     */
    private function getStudentExamsAndQuizzes(StudentEnrollment $enrollment): array
    {
        $examsList = [];

        // Get formal exams for the student's grade
        $exams = Exam::with([
                'schoolDay.semester',
                'subject',
                'grade'
            ])
            ->where('grade_id', $enrollment->section->grade_id)
            ->orderBy('school_day_id', 'asc')
            ->get();

        foreach ($exams as $exam) {
            $examsList[] = [
                'id' => $exam->id,
                'subject_name' => $exam->subject->name,
                'date' => $exam->schoolDay->date->format('Y-m-d'),
                'type' => $exam->type->getArabicName(),
                'semester' => $exam->schoolDay->semester->name
            ];
        }

        // Get quizzes targeted to the student's grade and section
        $quizTargets = QuizTarget::with([
                'quiz',
                'semester',
                'grade',
                'section',
                'subject'
            ])
            ->where('grade_id', $enrollment->section->grade_id)
            ->where('section_id', $enrollment->section_id)
            ->whereHas('quiz', function ($query) {
                $query->where('is_active', true);
            })
            ->get();

        foreach ($quizTargets as $quizTarget) {
            $examsList[] = [
                'id' => $quizTarget->quiz->id,
                'subject_name' => $quizTarget->subject ? $quizTarget->subject->name : $quizTarget->quiz->name,
                'date' => $quizTarget->quiz->taken_at ? 
                    \Carbon\Carbon::parse($quizTarget->quiz->taken_at)->format('Y-m-d') : 
                    now()->format('Y-m-d'),
                'type' => 'مذاكرة',
                'semester' => $quizTarget->semester->name
            ];
        }

        // Sort by date
        usort($examsList, function ($a, $b) {
            return strcmp($a['date'], $b['date']);
        });

        return $examsList;
    }
}
