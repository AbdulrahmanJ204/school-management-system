<?php

namespace App\Services;

use App\Exceptions\PermissionException;
use App\Helpers\ResponseHelper;
use App\Models\ClassSession;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\StudentEnrollment;
use App\Models\TeacherSectionSubject;
use App\Models\SchoolDay;
use App\Models\User;
use App\Models\Semester;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TeacherAttendanceTrackingService
{
    /**
     * Track all student attendance in class session
     */
    public function trackClassSessionAttendance(int $sessionId, Request $request): JsonResponse
    {
        // Get the class session
        $classSession = ClassSession::with(['section', 'subject', 'teacher', 'schoolDay'])
            ->findOrFail($sessionId);

        // Verify teacher authorization
        $this->verifyTeacherAuthorization($classSession->section_id, $classSession->subject_id);

        // Get the date parameter or use current day
        $date = $request->has('date') ? Carbon::parse($request->date) : Carbon::today();

        // Get current semester
        $currentSemester = Semester::where('is_active', true)->first();
        if (!$currentSemester) {
            return ResponseHelper::jsonResponse(
                null,
                'لا يوجد فصل دراسي نشط حالياً',
                404,
                false
            );
        }

        // Get students enrolled in the session's section
        $students = User::where('user_type', 'student')
            ->whereHas('student.studentEnrollments', function ($query) use ($classSession, $currentSemester) {
                $query->where('section_id', $classSession->section_id)
                    ->where('semester_id', $currentSemester->id);
            })
            ->with(['student'])
            ->orderBy('first_name', 'asc')
            ->get();

        // Get attendance records for this session and date (get most recent for each student)
        $attendanceRecords = StudentAttendance::whereHas('classSession', function ($query) use ($classSession, $date) {
                $query->where('section_id', $classSession->section_id)
                    ->where('subject_id', $classSession->subject_id)
                    ->whereHas('schoolDay', function ($q) use ($date) {
                        $q->where('date', $date->format('Y-m-d'));
                    });
            })
            ->with(['student'])
            ->orderBy('updated_at', 'desc')
            ->orderBy('id', 'desc')
            ->get()
            ->keyBy('student_id');

        // Calculate statistics
        $statistics = $this->calculateAttendanceStatistics($students, $attendanceRecords, $request->has('date'));

        // Build students data
        $studentsData = $students->map(function ($user) use ($attendanceRecords) {
            $attendance = $attendanceRecords->get($user->student->id);
            $status = $attendance ? $this->mapDatabaseStatusToFrontend($attendance->status) : 'present';

            return [
                'id' => $user->student->id,
                'name' => $user->first_name . ' ' . $user->last_name,
                'image' => $user->image ?? null,
                'status' => $status
            ];
        });

        // Add students to statistics
        $statistics['students'] = $studentsData;

        return ResponseHelper::jsonResponse(
            [
                'statistics' => $statistics
            ],
            'تم عرض حضور الطلاب بنجاح'
        );
    }

    /**
     * Store student attendance for class session
     * @throws PermissionException
     */
    public function storeClassSessionAttendance(int $sessionId, Request $request): JsonResponse
    {
        // Get the class session
        $classSession = ClassSession::with(['section', 'subject', 'schoolDay'])
            ->findOrFail($sessionId);

        // Verify teacher authorization
        $this->verifyTeacherAuthorization($classSession->section_id, $classSession->subject_id);

        $attendances = $request->input('attendances');
        $presentCount = 0;

        // First, clean up any duplicate records for this session to prevent issues
        $this->cleanupDuplicateAttendanceRecords($sessionId);

        // Store attendance records
        foreach ($attendances as $attendanceData) {
            $studentId = $attendanceData['student_id'];
            $frontendStatus = $attendanceData['status'];
            $databaseStatus = $this->mapFrontendStatusToDatabase($frontendStatus);

            // Count present students (note: lateness students are physically present but marked lateness)
            if ($databaseStatus === 'present') {
                $presentCount++;
            }

            // Delete existing records first to prevent duplicates, then create new one
            StudentAttendance::where('student_id', $studentId)
                ->where('class_session_id', $sessionId)
                ->delete();

            // Create new attendance record
            StudentAttendance::create([
                'student_id' => $studentId,
                'class_session_id' => $sessionId,
                'status' => $databaseStatus,
                'created_by' => auth()->id(),
            ]);
        }

        // Update class session present count
        $classSession->update(['present_students_count' => $presentCount]);

        // Get current semester
        $currentSemester = Semester::where('is_active', true)->first();

        // Get all students enrolled in the session's section to return complete data
        $allStudents = User::where('user_type', 'student')
            ->whereHas('student.studentEnrollments', function ($query) use ($classSession, $currentSemester) {
                $query->where('section_id', $classSession->section_id)
                    ->where('semester_id', $currentSemester->id);
            })
            ->with(['student'])
            ->orderBy('first_name', 'asc')
            ->get();

        // Get updated attendance records for this session (get most recent for each student)
        $updatedAttendanceRecords = StudentAttendance::where('class_session_id', $sessionId)
            ->orderBy('updated_at', 'desc')
            ->orderBy('id', 'desc')
            ->get()
            ->keyBy('student_id');

        // Calculate updated statistics
        $statistics = $this->calculateAttendanceStatisticsForSession($allStudents, $updatedAttendanceRecords);

        // Build updated students data
        $studentsData = $allStudents->map(function ($user) use ($updatedAttendanceRecords) {
            $attendance = $updatedAttendanceRecords->get($user->student->id);
            $status = $attendance ? $this->mapDatabaseStatusToFrontend($attendance->status) : 'present';

            return [
                'id' => $user->student->id,
                'name' => $user->first_name . ' ' . $user->last_name,
                'image' => $user->image ?? null,
                'status' => $status
            ];
        });

        // Add students to statistics
        $statistics['students'] = $studentsData;

        return ResponseHelper::jsonResponse(
            [
                'statistics' => $statistics
            ],
            'تم حفظ الحضور بنجاح'
        );
    }

    /**
     * Get attendance history for teacher's sections/subjects
     */
    public function getAttendanceHistory(Request $request): JsonResponse
    {
        $teacherId = auth()->user()->teacher->id;

        // Build query for teacher's assignments
        $query = TeacherSectionSubject::where('teacher_id', $teacherId)
            ->where('is_active', true);

        // Apply filters
        if ($request->has('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->has('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        $teacherAssignments = $query->get();

        // Get school days where teacher has recorded attendance
        $trackedDays = SchoolDay::whereHas('classSessions', function ($query) use ($teacherAssignments) {
                $query->where('teacher_id', auth()->user()->teacher->id)
                    ->whereHas('studentAttendances')
                    ->whereIn('section_id', $teacherAssignments->pluck('section_id'))
                    ->whereIn('subject_id', $teacherAssignments->pluck('subject_id'));
            })
            ->with(['classSessions' => function ($query) use ($teacherAssignments) {
                $query->where('teacher_id', auth()->user()->teacher->id)
                    ->whereHas('studentAttendances')
                    ->whereIn('section_id', $teacherAssignments->pluck('section_id'))
                    ->whereIn('subject_id', $teacherAssignments->pluck('subject_id'))
                    ->with(['classPeriod']);
            }])
            ->orderBy('date', 'desc')
            ->get();

        // Build response data
        $trackedDaysData = $trackedDays->map(function ($schoolDay) {
            $sessionsOrder = [];

            // Get sessions with their IDs and period orders, sorted by period order
            $sortedSessions = $schoolDay->classSessions
                ->sortBy('classPeriod.period_order');

            foreach ($sortedSessions as $session) {
                $periodOrder = $session->classPeriod->period_order;
                $arabicOrdinal = $this->convertNumberToArabicOrdinal($periodOrder);
                $sessionsOrder[$session->id] = $arabicOrdinal;
            }

            return [
                'id' => $schoolDay->id,
                'date' => $schoolDay->date->toISOString(),
                'sessionsOrder' => $sessionsOrder
            ];
        });

        return ResponseHelper::jsonResponse(
            ['trackedDays' => $trackedDaysData],
            'تم عرض سجل الحضور بنجاح'
        );
    }

    /**
     * Track individual student attendance
     */
    public function trackStudentAttendance(int $studentId): JsonResponse
    {
        $teacherId = auth()->user()->teacher->id;

        // Get student
        $student = Student::with(['user'])->findOrFail($studentId);

        // Get teacher's assignments for subjects this student is enrolled in
        $teacherAssignments = TeacherSectionSubject::where('teacher_id', $teacherId)
            ->where('is_active', true)
            ->whereHas('section.studentEnrollments.student', function ($query) use ($studentId) {
                $query->where('id', $studentId);
            })
            ->with(['subject', 'section'])
            ->get();

        if ($teacherAssignments->isEmpty()) {
            return ResponseHelper::jsonResponse(
                null,
                'غير مصرح لك بعرض حضور هذا الطالب',
                403,
                false
            );
        }

        // Build subjects data
        $subjectsData = $teacherAssignments->map(function ($assignment) use ($studentId) {
            $subject = $assignment->subject;
            $section = $assignment->section;

            // Get all class sessions for this subject and section
            $classSessions = ClassSession::where('subject_id', $subject->id)
                ->where('section_id', $section->id)
                ->where('teacher_id', auth()->user()->teacher->id)
                ->with(['schoolDay', 'classPeriod'])
                ->orderBy('created_at')
                ->get();

            // Get attendance records for this student
            $attendanceRecords = StudentAttendance::where('student_id', $studentId)
                ->whereIn('class_session_id', $classSessions->pluck('id'))
                ->with(['classSession.schoolDay', 'classSession.classPeriod'])
                ->get()
                ->keyBy('class_session_id');

            // Group by days
            $daysData = $classSessions->groupBy(function ($session) {
                return $session->schoolDay->date->format('Y-m-d');
            })->map(function ($daySessions, $date) use ($attendanceRecords) {
                $schoolDay = $daySessions->first()->schoolDay;
                $sessions = $daySessions->map(function ($session) use ($attendanceRecords) {
                    $attendance = $attendanceRecords->get($session->id);
                    $status = $attendance ? $this->mapDatabaseStatusToStudentTrackingFormat($attendance->status) : 'present';

                    return [
                        'sessionId' => $session->id,
                        'sessionNumber' => $this->convertNumberToArabicOrdinal($session->classPeriod->period_order),
                        'status' => $status
                    ];
                });

                return [
                    'dayId' => $schoolDay->id,
                    'date' => $date,
                    'sessions' => $sessions->values()
                ];
            });

            // Calculate statistics
            $stats = $this->calculateStudentSubjectStatistics($classSessions, $attendanceRecords);

            return [
                'subjectId' => $subject->id,
                'subjectName' => $subject->name,
                'subjectSession' => $assignment->num_class_period ?? 0,
                'days' => $daysData->values(),
                'stats' => $stats
            ];
        });

        return ResponseHelper::jsonResponse(
            ['subjects' => $subjectsData],
            'تم عرض حضور الطالب بنجاح'
        );
    }

    /**
     * Verify teacher authorization for section and subject
     */
    private function verifyTeacherAuthorization(int $sectionId, int $subjectId): void
    {
        if (!auth()->user()->teacher) {
            throw new PermissionException('المستخدم الحالي ليس أستاذاً');
        }

        $teacherId = auth()->user()->teacher->id;

        $isAuthorized = TeacherSectionSubject::where('teacher_id', $teacherId)
            ->where('section_id', $sectionId)
            ->where('subject_id', $subjectId)
            ->where('is_active', true)
            ->exists();

        if (!$isAuthorized) {
            throw new PermissionException('غير مصرح لك بالوصول إلى هذه الشعبة أو المادة');
        }
    }

    /**
     * Calculate attendance statistics
     */
    private function calculateAttendanceStatistics($students, $attendanceRecords, bool $isHistoricalData): array
    {
        $totalStudents = $students->count();
        $presentCount = 0;
        $absentCount = 0;
        $lateCount = 0;
        $excusedCount = 0;

        foreach ($students as $user) {
            $attendance = $attendanceRecords->get($user->student->id);
            if ($attendance) {
                switch ($attendance->status) {
                    case 'present':
                        $presentCount++;
                        break;
                    case 'absent':
                        $absentCount++;
                        break;
                    case 'lateness':
                        $lateCount++;
                        break;
                    case 'justified_absent':
                        $excusedCount++;
                        break;
                }
            } else {
                $presentCount++; // Assume present if no record
            }
        }

        return [
            'totalCount' => $totalStudents,
            'presentCount' => $presentCount,
            'absentCount' => $absentCount,
            'lateCount' => $lateCount,
            'excusedCount' => $isHistoricalData ? $excusedCount : -1
        ];
    }

    /**
     * Calculate attendance statistics for session (after storing attendance)
     */
    private function calculateAttendanceStatisticsForSession($students, $attendanceRecords): array
    {
        $totalStudents = $students->count();
        $presentCount = 0;
        $absentCount = 0;
        $lateCount = 0;
        $excusedCount = 0;

        foreach ($students as $user) {
            $attendance = $attendanceRecords->get($user->student->id);

            if ($attendance) {
                switch ($attendance->status) {
                    case 'present':
                        $presentCount++;
                        break;
                    case 'absent':
                        $absentCount++;
                        break;
                    case 'lateness':
                        $lateCount++;
                        break;
                    case 'justified_absent':
                        $excusedCount++;
                        break;
                }
            } else {
                $presentCount++; // Assume present if no record
            }
        }

        return [
            'totalCount' => $totalStudents,
            'presentCount' => $presentCount,
            'absentCount' => $absentCount,
            'lateCount' => $lateCount,
            'excusedCount' => $excusedCount // Always show excused count for session data
        ];
    }

    /**
     * Calculate statistics for a student in a specific subject
     */
    private function calculateStudentSubjectStatistics($classSessions, $attendanceRecords): array
    {
        $totalDays = $classSessions->count();

        if ($totalDays === 0) {
            return [
                'totalDays' => 0,
                'presentDays' => 0,
                'lateDays' => 0,
                'justifiedAbsentDays' => 0,
                'absentDays' => 0,
                'attendancePercentage' => 0.0,
                'justifiedAbsencePercentage' => 0.0,
                'absencePercentage' => 0.0,
                'latenessPercentage' => 0.0
            ];
        }

        $presentDays = 0;
        $lateDays = 0;
        $justifiedAbsentDays = 0;
        $absentDays = 0;

        foreach ($classSessions as $session) {
            $attendance = $attendanceRecords->get($session->id);
            if ($attendance) {
                switch ($attendance->status) {
                    case 'present':
                        $presentDays++;
                        break;
                    case 'lateness':
                        $lateDays++;
                        break;
                    case 'justified_absent':
                        $justifiedAbsentDays++;
                        break;
                    case 'absent':
                        $absentDays++;
                        break;
                }
            } else {
                $presentDays++; // Assume present if no record
            }
        }

        return [
            'totalDays' => $totalDays,
            'presentDays' => $presentDays,
            'lateDays' => $lateDays,
            'justifiedAbsentDays' => $justifiedAbsentDays,
            'absentDays' => $absentDays,
            'attendancePercentage' => round(($presentDays / $totalDays) * 100, 2),
            'justifiedAbsencePercentage' => round(($justifiedAbsentDays / $totalDays) * 100, 2),
            'absencePercentage' => round(($absentDays / $totalDays) * 100, 2),
            'latenessPercentage' => round(($lateDays / $totalDays) * 100, 2)
        ];
    }

    /**
     * Map database status to frontend format
     */
    private function mapDatabaseStatusToFrontend(string $status): string
    {
        $mapping = match ($status) {
            'present' => 'present',
            'absent' => 'absent',
            'lateness' => 'lateness',
            'justified_absent' => 'justified_absent',
            default => 'present',
        };

        return $mapping;
    }

    /**
     * Map frontend status to database format
     */
    private function mapFrontendStatusToDatabase(string $status): string
    {
        return match ($status) {
            'present' => 'present',
            'absent' => 'absent',
            'lateness' => 'lateness',
            default => 'present',
        };
    }

    /**
     * Map database status to student tracking format (for individual student attendance)
     */
    private function mapDatabaseStatusToStudentTrackingFormat(string $status): string
    {
        return match ($status) {
            'present' => 'present',
            'absent' => 'absent',
            'lateness' => 'lateness',
            'justified_absent' => 'justifiedAbsent',
            default => 'present',
        };
    }

    /**
     * Convert number to Arabic ordinal
     */
    private function convertNumberToArabicOrdinal(int $number): string
    {
        $arabicOrdinals = [
            1 => 'الأولى',
            2 => 'الثانية',
            3 => 'الثالثة',
            4 => 'الرابعة',
            5 => 'الخامسة',
            6 => 'السادسة',
            7 => 'السابعة',
            8 => 'الثامنة',
            9 => 'التاسعة',
            10 => 'العاشرة',
            11 => 'الحادية عشر',
            12 => 'الثانية عشر',
            13 => 'الثالثة عشر',
            14 => 'الرابعة عشر',
            15 => 'الخامسة عشر',
            16 => 'السادسة عشر',
            17 => 'السابعة عشر',
            18 => 'الثامنة عشر',
            19 => 'التاسعة عشر',
            20 => 'العشرون'
        ];

        return $arabicOrdinals[$number] ?? (string) $number;
    }

    /**
     * Clean up duplicate attendance records for a session
     */
    private function cleanupDuplicateAttendanceRecords(int $sessionId): void
    {
        // Get all attendance records for this session
        $records = StudentAttendance::where('class_session_id', $sessionId)
            ->orderBy('student_id')
            ->orderBy('updated_at', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $keepRecords = [];
        $deleteRecords = [];

        // Group by student_id and keep only the most recent record for each student
        foreach ($records as $record) {
            if (!isset($keepRecords[$record->student_id])) {
                $keepRecords[$record->student_id] = $record->id;
            } else {
                $deleteRecords[] = $record->id;
            }
        }

        // Delete duplicate records
        if (!empty($deleteRecords)) {
            StudentAttendance::whereIn('id', $deleteRecords)->delete();
        }
    }
}
