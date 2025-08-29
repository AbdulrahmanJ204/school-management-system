<?php

namespace App\Services;

use App\Enums\PermissionEnum;
use App\Exceptions\PermissionException;
use App\Helpers\ResponseHelper;
use App\Http\Resources\StudentReportResource;
use App\Http\Requests\Admin\GetStudentReportRequest;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\StudentMark;
use App\Models\MainSubject;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class StudentReportService
{
    /**
     * Generate comprehensive student report for admin
     * 
     * @param GetStudentReportRequest $request
     * @return JsonResponse
     * @throws PermissionException
     */
    public function generateStudentReport(GetStudentReportRequest $request): JsonResponse
    {
        // Check admin permissions
        if (!auth()->user()->hasPermissionTo(PermissionEnum::VIEW_STUDENT_REPORT->value)) {
            throw new PermissionException();
        }

        $studentId = $request->student_id;
        $semesterId = $request->semester_id;

        // Get student enrollment for the specific semester
        $enrollment = StudentEnrollment::where('student_id', $studentId)
            ->where('semester_id', $semesterId)
            ->with(['student.user', 'section', 'grade', 'semester'])
            ->first();

        if (!$enrollment) {
            return ResponseHelper::jsonResponse(
                null,
                __('messages.student.enrollment.not-found'),
                404
            );
        }

        // Get all student marks for this enrollment with subjects and main subjects
        $studentMarks = StudentMark::where('enrollment_id', $enrollment->id)
            ->with(['subject.mainSubject'])
            ->get();

        if ($studentMarks->isEmpty()) {
            return ResponseHelper::jsonResponse(
                null,
                __('messages.student.marks.no-marks-found'),
                404
            );
        }

        // Group marks by main subject and calculate totals
        $mainSubjectsData = $this->calculateMainSubjectTotals($studentMarks);
        
        // Calculate overall performance
        $overallPerformance = $this->calculateOverallPerformance($mainSubjectsData);

        // Prepare the report data
        $reportData = [
            'student' => $enrollment->student,
            'section' => $enrollment->section,
            'grade' => $enrollment->grade,
            'semester' => $enrollment->semester,
            'mainSubjects' => $mainSubjectsData,
            'overallPerformance' => $overallPerformance
        ];

        return ResponseHelper::jsonResponse(
            new StudentReportResource($reportData),
            __('messages.student.report.generated'),
            200
        );
    }

    /**
     * Calculate totals for each main subject
     * 
     * @param \Illuminate\Support\Collection $studentMarks
     * @return array
     */
    private function calculateMainSubjectTotals($studentMarks): array
    {
        $mainSubjectsData = [];

        // Group marks by main subject
        $groupedMarks = $studentMarks->groupBy('subject.mainSubject.id');

        foreach ($groupedMarks as $mainSubjectId => $marks) {
            $mainSubject = $marks->first()->subject->mainSubject;
            
            $totalMarks = 0;
            $totalFullMarks = 0;
            $passedSubjects = 0;
            $failedSubjects = 0;

            foreach ($marks as $mark) {
                $totalMarks += $mark->total ?? 0;
                $totalFullMarks += $mark->subject->full_mark ?? 0;
                
                // Determine if subject is passed based on success rate
                $subjectPercentage = $mark->subject->full_mark > 0 ? 
                    (($mark->total ?? 0) / $mark->subject->full_mark) * 100 : 0;
                
                if ($subjectPercentage >= $mainSubject->success_rate) {
                    $passedSubjects++;
                } else {
                    $failedSubjects++;
                }
            }

            $successRate = $totalFullMarks > 0 ? 
                round(($totalMarks / $totalFullMarks) * 100, 2) : 0;

            $mainSubjectsData[] = [
                'id' => $mainSubject->id,
                'name' => $mainSubject->name,
                'code' => $mainSubject->code,
                'total_marks' => $totalMarks,
                'full_mark' => $totalFullMarks,
                'success_rate' => $successRate,
                'status' => $successRate >= $mainSubject->success_rate ? 'passed' : 'failed',
                'passed_subjects' => $passedSubjects,
                'failed_subjects' => $failedSubjects
            ];
        }

        return $mainSubjectsData;
    }

    /**
     * Calculate overall performance metrics
     * 
     * @param array $mainSubjectsData
     * @return array
     */
    private function calculateOverallPerformance(array $mainSubjectsData): array
    {
        $totalMarks = 0;
        $totalFullMarks = 0;
        $passedSubjects = 0;
        $failedSubjects = 0;

        foreach ($mainSubjectsData as $mainSubject) {
            $totalMarks += $mainSubject['total_marks'];
            $totalFullMarks += $mainSubject['full_mark'];
            $passedSubjects += $mainSubject['status'] == 'passed' ? 1 : 0;
            $failedSubjects += $mainSubject['status'] == 'failed' ? 1 : 0;
        }

        $averagePercentage = $totalFullMarks > 0 ? 
            round(($totalMarks / $totalFullMarks) * 100, 2) : 0;

        return [
            'total_marks' => $totalMarks,
            'total_full_marks' => $totalFullMarks,
            'average_percentage' => $averagePercentage,
            'passed_main_subjects' => $passedSubjects,
            'failed_main_subjects' => $failedSubjects
        ];
    }
}
