<?php

namespace App\Services;

use App\Exceptions\PermissionException;
use App\Helpers\ResponseHelper;
use App\Http\Resources\UserResource;
use App\Http\Resources\StudentProfileResource;
use App\Models\User;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\StudentMark;
use App\Models\StudentAttendance;
use App\Models\ClassSession;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class StudentService
{
    /**
     * @throws PermissionException
     */
    public function listStudents(): JsonResponse
    {
        if (!auth()->user()->hasPermissionTo('عرض الطلاب')) {
            throw new PermissionException();
        }

        $students = User::where('user_type', 'student')
            ->with(['devices', 'student'])
            ->orderBy('first_name', 'asc')
            ->paginate(50);

        return ResponseHelper::jsonResponse(
            UserResource::collection($students),
            __('messages.student.listed'),
            200,
            true,
            $students->lastPage()
        );
    }

    /**
     * @throws PermissionException
     */
    public function getStudentsBySectionAndSemester($sectionId, $semesterId): JsonResponse
    {
        if (!auth()->user()->hasPermissionTo('عرض الطلاب')) {
            throw new PermissionException();
        }

        $students = User::where('user_type', 'student')
            ->whereHas('student.studentEnrollments', function ($query) use ($sectionId, $semesterId) {
                $query->where('section_id', $sectionId)
                    ->where('semester_id', $semesterId);
            })
            ->with(['devices', 'student.studentEnrollments' => function ($query) use ($sectionId, $semesterId) {
                $query->where('section_id', $sectionId)
                    ->where('semester_id', $semesterId)
                    ->with(['section', 'semester', 'year']);
            }])
            ->orderBy('first_name', 'asc')
            ->get();

        return ResponseHelper::jsonResponse(
            UserResource::collection($students),
        );
    }

    /**
     * Get authenticated student's own profile with statistics
     * @throws PermissionException
     */
    public function getMyProfile(): JsonResponse
    {
        $user = auth()->user();

        // Ensure the authenticated user is a student
        if ($user->user_type !== 'student') {
            throw new PermissionException();
        }

        $student = $user->student;
        if (!$student) {
            return ResponseHelper::jsonResponse(
                null,
                'Student record not found for this user',
                404,
                false
            );
        }

        $student->load(['user', 'studentEnrollments.section.grade', 'studentEnrollments.semester.year']);

        // Get current enrollment (most recent)
        $currentEnrollment = $student->studentEnrollments()
            ->with(['section.grade', 'semester.year'])
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$currentEnrollment) {
            return ResponseHelper::jsonResponse(
                null,
                'Student has no enrollment records',
                404,
                false
            );
        }

        // Calculate age
        $age = Carbon::parse($student->user->birth_date)->age;

        // Get class information

        $className = $currentEnrollment->section->grade->title ?? 'N/A';
        $sectionNumber = $currentEnrollment->section->title ?? 'N/A';

        // Calculate GPA percentage
        $gpaPercentage = $this->calculateGpaPercentage($currentEnrollment);

        // Calculate rankings
        $rankInSection = $this->calculateRankInSection($currentEnrollment);
        $rankAcrossSections = $this->calculateRankAcrossSections($currentEnrollment);

        // Calculate attendance statistics
        $attendanceStats = $this->calculateAttendanceStatistics($student);

        $profileData = [
            'user' => $student->user,
            'age' => $age,
            'className' => $className,
            'sectionNumber' => $sectionNumber,
            'rankInSection' => $rankInSection,
            'rankAcrossSections' => $rankAcrossSections,
            'gpaPercentage' => $gpaPercentage,
            'attendancePercentage' => $attendanceStats['attendancePercentage'],
            'absencePercentage' => $attendanceStats['absencePercentage'],
            'justifiedAbsencePercentage' => $attendanceStats['justifiedAbsencePercentage'],
            'latenessPercentage' => $attendanceStats['latenessPercentage'],
        ];

        return ResponseHelper::jsonResponse(
            new StudentProfileResource($profileData),
            'Student profile retrieved successfully',
            200,
            true
        );
    }

    /**
     * Calculate GPA percentage based on student marks (only quiz and exam)
     */
    private function calculateGpaPercentage(StudentEnrollment $enrollment): float
    {
        $marks = StudentMark::where('enrollment_id', $enrollment->id)
            ->with('subject')
            ->get();

        if ($marks->isEmpty()) {
            return 0.0;
        }

        $totalWeightedMarks = 0;
        $totalPossibleMarks = 0;

        foreach ($marks as $mark) {
            $subject = $mark->subject;
            
            // Calculate weighted marks for this subject (only quiz and exam)
            $subjectWeightedMarks = 0;
            $subjectPossibleMarks = 0;
            
            if ($mark->quiz !== null) {
                $subjectWeightedMarks += ($mark->quiz * $subject->quiz_percentage) / 100;
                $subjectPossibleMarks += $subject->quiz_percentage;
            }
            if ($mark->exam !== null) {
                $subjectWeightedMarks += ($mark->exam * $subject->exam_percentage) / 100;
                $subjectPossibleMarks += $subject->exam_percentage;
            }
            
            $totalWeightedMarks += $subjectWeightedMarks;
            $totalPossibleMarks += $subjectPossibleMarks;
        }

        return $totalPossibleMarks > 0 ? round(($totalWeightedMarks / $totalPossibleMarks) * 100, 2) : 0.0;
    }

    /**
     * Calculate rank within section
     */
    private function calculateRankInSection(StudentEnrollment $enrollment): int
    {
        // Get all students in the same section and semester
        $sectionEnrollments = StudentEnrollment::where('section_id', $enrollment->section_id)
            ->where('semester_id', $enrollment->semester_id)
            ->get();

        // Calculate GPA for each student and rank them
        $studentsWithGpa = $sectionEnrollments->map(function ($enrollment) {
            $gpa = $this->calculateGpaPercentage($enrollment);

            return [
                'enrollment_id' => $enrollment->id,
                'gpa' => $gpa
            ];
        })->sortByDesc('gpa')->values();

        // Find the rank of current student
        $rank = $studentsWithGpa->search(function ($item) use ($enrollment) {
            return $item['enrollment_id'] === $enrollment->id;
        });

        return $rank !== false ? $rank + 1 : $sectionEnrollments->count();
    }

    /**
     * Calculate rank across all sections in the same grade
     */
    private function calculateRankAcrossSections(StudentEnrollment $enrollment): int
    {
        // Get all students in the same grade and semester
        $gradeEnrollments = StudentEnrollment::whereHas('section', function ($query) use ($enrollment) {
            $query->where('grade_id', $enrollment->section->grade_id);
        })
            ->where('semester_id', $enrollment->semester_id)
            ->get();

        // Calculate GPA for each student and rank them
        $studentsWithGpa = $gradeEnrollments->map(function ($enrollment) {
            $gpa = $this->calculateGpaPercentage($enrollment);

            return [
                'enrollment_id' => $enrollment->id,
                'gpa' => $gpa
            ];
        })->sortByDesc('gpa')->values();

        // Find the rank of current student
        $rank = $studentsWithGpa->search(function ($item) use ($enrollment) {
            return $item['enrollment_id'] === $enrollment->id;
        });

        return $rank !== false ? $rank + 1 : $gradeEnrollments->count();
    }

        /**
     * Calculate attendance statistics
     */
    private function calculateAttendanceStatistics(Student $student): array
    {
        // Get current enrollment to determine which class sessions the student should attend
        $currentEnrollment = $student->studentEnrollments()
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$currentEnrollment) {
            return [
                'attendancePercentage' => 0.0,
                'absencePercentage' => 0.0,
                'justifiedAbsencePercentage' => 0.0,
                'latenessPercentage' => 0.0,
            ];
        }

        // Get all class sessions for the student's section
        $totalClassSessions = ClassSession::where('section_id', $currentEnrollment->section_id)
            ->where('status', 'completed')
            ->count();

        if ($totalClassSessions === 0) {
            return [
                'attendancePercentage' => 0.0,
                'absencePercentage' => 0.0,
                'justifiedAbsencePercentage' => 0.0,
                'latenessPercentage' => 0.0,
            ];
        }

        // Get attendance records for the student (only absences and late records exist)
        $attendanceRecords = StudentAttendance::where('student_id', $student->id)
            ->whereHas('classSession', function($query) use ($currentEnrollment) {
                $query->where('section_id', $currentEnrollment->section_id)
                      ->where('status', 'completed');
            })
            ->get();
        
        $excusedAbsences = $attendanceRecords->where('status', 'Excused absence')->count();
        $unexcusedAbsences = $attendanceRecords->where('status', 'Unexcused absence')->count();
        $lateRecords = $attendanceRecords->where('status', 'Late')->count();
        
        $totalAbsences = $excusedAbsences + $unexcusedAbsences;
        
        // Present sessions = total sessions - absences (late is considered present but late)
        $presentSessions = $totalClassSessions - $totalAbsences;

        return [
            'attendancePercentage' => $totalClassSessions > 0 ? round(($presentSessions / $totalClassSessions) * 100, 2) : 0.0,
            'absencePercentage' => $totalClassSessions > 0 ? round(($totalAbsences / $totalClassSessions) * 100, 2) : 0.0,
            'justifiedAbsencePercentage' => $totalClassSessions > 0 ? round(($excusedAbsences / $totalClassSessions) * 100, 2) : 0.0,
            'latenessPercentage' => $totalClassSessions > 0 ? round(($lateRecords / $totalClassSessions) * 100, 2) : 0.0,
        ];
    }
}
