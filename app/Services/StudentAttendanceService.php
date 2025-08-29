<?php

namespace App\Services;

use App\Enums\PermissionEnum;
use App\Exceptions\PermissionException;
use App\Helpers\ResponseHelper;
use App\Http\Requests\ListDailyAttendanceRequest;
use App\Http\Requests\ListSessionsAttendanceRequest;
use App\Http\Requests\UpdateDailyAttendanceRequest;
use App\Http\Requests\UpdateSessionsAttendanceRequest;
use App\Http\Resources\StudentAttendanceResource;
use App\Models\ClassSession;
use App\Models\SchoolDay;
use App\Models\Semester;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\Year;
use App\Traits\HasPermissionChecks;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class StudentAttendanceService
{
    use HasPermissionChecks;

    /**
     * @throws PermissionException
     */
    public function listStudentAttendances(Request $request): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_STUDENT_ATTENDANCES);

        $query = StudentAttendance::with(['student.user', 'classSession', 'createdBy']);

        // Apply filters
        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->has('class_session_id')) {
            $query->where('class_session_id', $request->class_session_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from')) {
            $query->whereHas('classSession', function ($q) use ($request) {
                $q->where('date', '>=', $request->date_from);
            });
        }

        if ($request->has('date_to')) {
            $query->whereHas('classSession', function ($q) use ($request) {
                $q->where('date', '<=', $request->date_to);
            });
        }

        $studentAttendances = $query->orderBy('created_at', 'desc')->paginate(15);

        return ResponseHelper::jsonResponse(
            StudentAttendanceResource::collection($studentAttendances),
            'تم عرض حضور الطلاب بنجاح'
        );
    }

    /**
     * @throws PermissionException
     */
    public function createStudentAttendance(Request $request): JsonResponse
    {
        $this->checkPermission(PermissionEnum::CREATE_STUDENT_ATTENDANCE);

        $data = $request->validated();
        $data['created_by'] = auth()->id();

        $studentAttendance = StudentAttendance::create($data);

        return ResponseHelper::jsonResponse(
            new StudentAttendanceResource($studentAttendance->load(['student.user', 'classSession', 'createdBy'])),
            'تم إضافة حضور الطالب بنجاح',
            ResponseAlias::HTTP_CREATED,
        );
    }

    /**
     * @throws PermissionException
     */
    public function showStudentAttendance($id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_STUDENT_ATTENDANCE);

        $studentAttendance = StudentAttendance::with(['student.user', 'classSession', 'createdBy'])
            ->findOrFail($id);

        return ResponseHelper::jsonResponse(
            new StudentAttendanceResource($studentAttendance),
            'تم عرض حضور الطالب بنجاح'
        );
    }

    /**
     * @throws PermissionException
     */
    public function updateStudentAttendance(Request $request, $id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::UPDATE_STUDENT_ATTENDANCE);

        $studentAttendance = StudentAttendance::findOrFail($id);
        $data = $request->validated();

        $studentAttendance->update($data);

        return ResponseHelper::jsonResponse(
            new StudentAttendanceResource($studentAttendance->load(['student.user', 'classSession', 'createdBy'])),
            'تم تحديث حضور الطالب بنجاح'
        );
    }

    /**
     * @throws PermissionException
     */
    public function deleteStudentAttendance($id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::DELETE_STUDENT_ATTENDANCE);

        $studentAttendance = StudentAttendance::findOrFail($id);
        $studentAttendance->delete();

        return ResponseHelper::jsonResponse(
            null,
            'تم حذف حضور الطالب بنجاح'
        );
    }

    /**
     * @throws PermissionException
     */
    public function getByStudent($studentId): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_STUDENT_ATTENDANCES);

        $studentAttendances = StudentAttendance::where('student_id', $studentId)
            ->with(['student.user', 'classSession', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return ResponseHelper::jsonResponse(
            StudentAttendanceResource::collection($studentAttendances),
            'تم عرض حضور الطالب بنجاح'
        );
    }

    /**
     * @throws PermissionException
     */
    public function getByClassSession($classSessionId): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_STUDENT_ATTENDANCES);

        $studentAttendances = StudentAttendance::where('class_session_id', $classSessionId)
            ->with(['student.user', 'classSession', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return ResponseHelper::jsonResponse(
            StudentAttendanceResource::collection($studentAttendances),
            'تم عرض حضور الطلاب في الجلسة بنجاح'
        );
    }

    /**
     * Generate detailed attendance report for a student for all months in current year
     */
    public function generateAttendanceReport(): JsonResponse
    {
        // Get student_id from authenticated user
        $user = auth()->user();
        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            return ResponseHelper::jsonResponse(
                ['months' => []],
                'لم يتم العثور على بيانات الطالب',
                ResponseAlias::HTTP_NOT_FOUND,
                false
            );
        }

        $studentId = $student->id;

        // Get all semesters data for the current year
        $monthsData = [];

        // Get the current academic year from the Year model
        $currentAcademicYear = Year::where('is_active', true)->first();

        if (!$currentAcademicYear) {
            return ResponseHelper::jsonResponse(
                ['months' => []],
                'لم يتم العثور على السنة الدراسية الحالية',
                ResponseAlias::HTTP_NOT_FOUND,
                false
            );
        }

        // Get all semesters for the current academic year
        $semesters = Semester::where('year_id', $currentAcademicYear->id)
            ->orderBy('start_date')
            ->get();

        if ($semesters->isEmpty()) {
            return ResponseHelper::jsonResponse(
                ['months' => []],
                'لم يتم العثور على فصول دراسية في السنة الحالية',
                ResponseAlias::HTTP_NOT_FOUND,
                false
            );
        }

        // Generate data for each semester, breaking down into months
        foreach ($semesters as $semester) {
            $semesterMonths = $this->generateSemesterMonthsData($studentId, $semester);
            if ($semesterMonths) {
                $monthsData = array_merge($monthsData, $semesterMonths);
            }
        }

        return ResponseHelper::jsonResponse(
            ['months' => $monthsData],
            'تم إنشاء تقرير الحضور بنجاح'
        );
    }

    /**
     * Generate data for a specific semester, broken down into individual months
     */
    private function generateSemesterMonthsData($studentId, $semester): ?array
    {
        // Get all school days for the specified semester
        $schoolDays = SchoolDay::where('semester_id', $semester->id)
            ->with(['semester'])
            ->orderBy('date')
            ->get();

        if ($schoolDays->isEmpty()) {
            return null; // No school days in this semester
        }

        // Get student enrollment for this semester
        $student = Student::with(['user', 'studentEnrollments.section.grade'])
            ->find($studentId);

        $studentEnrollment = $student->studentEnrollments()
            ->where('semester_id', $semester->id)
            ->first();

        if (!$studentEnrollment) {
            return null; // No enrollment for this semester
        }

        $classSessions = ClassSession::where('section_id', $studentEnrollment->section_id)
            ->whereHas('schoolDay', function ($query) use ($semester) {
                $query->where('semester_id', $semester->id);
            })
            ->with(['schoolDay', 'teacher.user', 'subject', 'classPeriod'])
            ->orderBy('created_at')
            ->get();

        // Get student attendances for this semester
        $studentAttendances = StudentAttendance::where('student_id', $studentId)
            ->whereHas('classSession', function ($query) use ($semester) {
                $query->whereHas('schoolDay', function ($q) use ($semester) {
                    $q->where('semester_id', $semester->id);
                });
            })
            ->with(['classSession.schoolDay', 'classSession.teacher.user', 'classSession.subject', 'classSession.classPeriod'])
            ->get()
            ->keyBy(function ($attendance) {
                return $attendance->classSession->schoolDay->date->format('Y-m-d') . '_' . $attendance->classSession->classPeriod->id;
            });

        // Group school days by month
        $monthsData = [];
        $schoolDaysByMonth = $schoolDays->groupBy(function ($schoolDay) {
            return $schoolDay->date->format('Y-m');
        });

        foreach ($schoolDaysByMonth as $yearMonth => $monthSchoolDays) {
            $year = (int) substr($yearMonth, 0, 4);
            $month = (int) substr($yearMonth, 5, 2);

            // Filter class sessions for this month
            $monthClassSessions = $classSessions->filter(function ($session) use ($year, $month) {
                return $session->schoolDay->date->year === $year && $session->schoolDay->date->month === $month;
            });

            // Filter student attendances for this month
            $monthStudentAttendances = $studentAttendances->filter(function ($attendance) use ($year, $month) {
                return $attendance->classSession->schoolDay->date->year === $year && $attendance->classSession->schoolDay->date->month === $month;
            });

            // Build month data
            $monthData = $this->buildMonthData($monthSchoolDays, $monthClassSessions, $monthStudentAttendances, $student, $year, $month);
            if ($monthData) {
                $monthsData[] = $monthData;
            }
        }

        return $monthsData;
    }

    /**
     * Build month data with statistics and daily details
     */
    private function buildMonthData($schoolDays, $classSessions, $studentAttendances, $student, $year, $month): array
    {
        $stats = $this->calculateMonthStats($schoolDays, $classSessions, $studentAttendances);
        $days = $this->buildDaysData($schoolDays, $classSessions, $studentAttendances);

        return [
            'year' => $year,
            'month' => $month,
            'stats' => $stats,
            'days' => $days
        ];
    }

    /**
     * Calculate monthly attendance statistics
     */
    private function calculateMonthStats($schoolDays, $classSessions, $studentAttendances): array
    {
        $totalDays = $schoolDays->where('type', 'study')->count();
        $totalSessions = $classSessions->count();

        // Count explicit attendance records
        $presentSessions = $studentAttendances->where('status', 'present')->count();
        $absentSessions = $studentAttendances->where('status', 'absent')->count();
        $justifiedAbsentSessions = $studentAttendances->where('status', 'justified_absent')->count();
        $lateSessions = $studentAttendances->where('status', 'lateness')->count();

        // For sessions without explicit attendance records, assume present (old system behavior)
        $sessionsWithRecords = $presentSessions + $absentSessions + $justifiedAbsentSessions + $lateSessions;
        $sessionsWithoutRecords = $totalSessions - $sessionsWithRecords;
        $presentSessions += $sessionsWithoutRecords; // Add sessions without records as present

        $totalAttendanceSessions = $totalSessions; // Use total sessions for percentage calculation

        return [
            'attendancePercentage' => $totalAttendanceSessions > 0 ? round(($presentSessions / $totalAttendanceSessions) * 100, 1) : 0,
            'absencePercentage' => $totalAttendanceSessions > 0 ? round(($absentSessions / $totalAttendanceSessions) * 100, 1) : 0,
            'justifiedAbsencePercentage' => $totalAttendanceSessions > 0 ? round(($justifiedAbsentSessions / $totalAttendanceSessions) * 100, 1) : 0,
            'latenessPercentage' => $totalAttendanceSessions > 0 ? round(($lateSessions / $totalAttendanceSessions) * 100, 1) : 0,
            'totalDays' => $totalDays,
            'presentDays' => $this->countPresentDays($schoolDays, $studentAttendances, $classSessions),
            'absentDays' => $this->countAbsentDays($schoolDays, $studentAttendances),
            'justifiedAbsentDays' => $this->countJustifiedAbsentDays($schoolDays, $studentAttendances),
            'lateDays' => $this->countLateDays($schoolDays, $studentAttendances)
        ];
    }

    /**
     * Build daily attendance data
     */
    private function buildDaysData($schoolDays, $classSessions, $studentAttendances): array
    {
        $days = [];

        foreach ($schoolDays as $schoolDay) {
            $date = $schoolDay->date->format('Y-m-d');
            $daySessions = $classSessions->where('school_day_id', $schoolDay->id);

            if ($schoolDay->type === 'holiday') {
                $days[] = [
                    'date' => $date,
                    'status' => 'holiday',
                    'sessions' => []
                ];
            } else {
                $sessions = $this->buildSessionsData($daySessions, $studentAttendances, $date);
                $dayStatus = $this->determineDayStatus($daySessions, $studentAttendances, $date);

                $days[] = [
                    'date' => $date,
                    'status' => $dayStatus,
                    'sessions' => $sessions
                ];
            }
        }

        return $days;
    }

    /**
     * Build sessions data for a specific day
     */
    private function buildSessionsData($daySessions, $studentAttendances, $date): array
    {
        $sessions = [];

        foreach ($daySessions as $session) {
            $attendanceKey = $date . '_' . $session->classPeriod->id;
            $attendance = $studentAttendances->get($attendanceKey);

            $sessions[] = [
                'sessionNumber' => $session->classPeriod->period_number ?? 1,
                'teacherName' => $session->teacher->user->name ?? 'غير محدد',
                'subjectName' => $session->subject->name ?? 'غير محدد',
                'status' => $this->mapAttendanceStatus($attendance ? $attendance->status : null)
            ];
        }

        return $sessions;
    }

    /**
     * Determine overall day status
     */
    private function determineDayStatus($daySessions, $studentAttendances, $date): string
    {
        if ($daySessions->isEmpty()) {
            return 'notOccurredYet';
        }


        $sessionsWithRecords = 0;
        $presentCount = 0;
        $absentCount = 0;
        $justifiedAbsentCount = 0;
        $lateCount = 0;

        foreach ($daySessions as $session) {
            $attendanceKey = $date . '_' . $session->classPeriod->id;
            $attendance = $studentAttendances->get($attendanceKey);

            if ($attendance) {
                $sessionsWithRecords++;
                switch ($attendance->status) {
                    case 'present':
                        $presentCount++;
                        break;
                    case 'absent':
                    case 'justified_absent':
                        $justifiedAbsentCount++;
                        break;
                    case 'lateness':
                        $lateCount++;
                        break;
                }
            }
        }

        // If no attendance records exist for any session, assume present (old system behavior)
        if ($sessionsWithRecords === 0) {
            return 'present';
        }

        return $this->determineDayStatusByCount($presentCount, $sessionsWithRecords, $absentCount, $justifiedAbsentCount, $lateCount);
    }

    /**
     * Map attendance status to frontend format
     */
    private function mapAttendanceStatus($status): string
    {
        return match ($status) {
            'present' => 'present',
            'absent' => 'absent',
            'justified_absent' => 'justified_absent',
            'lateness' => 'lateness',
            default => 'present',
        };
    }

    /**
     * Count present days
     */
    private function countPresentDays($schoolDays, $studentAttendances, $classSessions): int
    {
        $presentDays = 0;

        foreach ($schoolDays->where('type', 'study') as $schoolDay) {
            $date = $schoolDay->date->format('Y-m-d');
            $daySessions = $classSessions->where('school_day_id', $schoolDay->id);
            $dayAttendances = $studentAttendances->filter(function ($attendance) use ($date) {
                return $attendance->classSession->schoolDay->date->format('Y-m-d') === $date;
            });

            // Count sessions with explicit present records
            $explicitPresentSessions = $dayAttendances->where('status', 'present')->count();

            // Count sessions without any attendance records (assume present in old system)
            $sessionsWithRecords = $dayAttendances->count();
            $sessionsWithoutRecords = $daySessions->count() - $sessionsWithRecords;

            // If there are any present sessions or sessions without records, count as present day
            if ($explicitPresentSessions > 0 || $sessionsWithoutRecords > 0) {
                $presentDays++;
            }
        }

        return $presentDays;
    }

    /**
     * Count absent days
     */
    private function countAbsentDays($schoolDays, $studentAttendances): int
    {
        $absentDays = 0;

        foreach ($schoolDays->where('type', 'study') as $schoolDay) {
            $date = $schoolDay->date->format('Y-m-d');
            $dayAttendances = $studentAttendances->filter(function ($attendance) use ($date) {
                return $attendance->classSession->schoolDay->date->format('Y-m-d') === $date;
            });

            if ($dayAttendances->where('status', 'absent')->count() > 0) {
                $absentDays++;
            }
        }

        return $absentDays;
    }

    /**
     * Count justified absent days
     */
    private function countJustifiedAbsentDays($schoolDays, $studentAttendances): int
    {
        $justifiedAbsentDays = 0;

        foreach ($schoolDays->where('type', 'study') as $schoolDay) {
            $date = $schoolDay->date->format('Y-m-d');
            $dayAttendances = $studentAttendances->filter(function ($attendance) use ($date) {
                return $attendance->classSession->schoolDay->date->format('Y-m-d') === $date;
            });

            if ($dayAttendances->where('status', 'justified_absent')->count() > 0) {
                $justifiedAbsentDays++;
            }
        }

        return $justifiedAbsentDays;
    }

    /**
     * Count lateness days
     */
    private function countLateDays($schoolDays, $studentAttendances): int
    {
        $lateDays = 0;

        foreach ($schoolDays->where('type', 'study') as $schoolDay) {
            $date = $schoolDay->date->format('Y-m-d');
            $dayAttendances = $studentAttendances->filter(function ($attendance) use ($date) {
                return $attendance->classSession->schoolDay->date->format('Y-m-d') === $date;
            });

            if ($dayAttendances->where('status', 'lateness')->count() > 0) {
                $lateDays++;
            }
        }

        return $lateDays;
    }



    public function updateDailyStudentsAttendance(UpdateDailyAttendanceRequest $request): JsonResponse
    {

        $data = $request->validated();
        $sessions = ClassSession::where('school_day_id', $data['school_day_id'])
            ->where('section_id', $data['section_id'])->get();
        $returnedData = [];

        foreach ($data['students'] as $student) {
            foreach ($sessions as $session) {
                $record = StudentAttendance::where('student_id', $student['id'])
                    ->where('class_session_id', $session->id)->first();
                if ($record) {
                    $record->update([
                        'status' => $student['status'],
                    ]);
                } else {
                    StudentAttendance::create([
                        'student_id' => $student['id'],
                        'class_session_id' => $session->id,
                        'status' => $student['status'],
                        'created_by' => auth()->id(),
                    ]);
                }
                $returnedData[] = $record;
            }
        }



        return ResponseHelper::jsonResponse(
            $returnedData,
            'تم تحديث حضور الطلاب بنجاح'
        );
    }

    public function updateSessionsStudentsAttendance(UpdateSessionsAttendanceRequest $request): JsonResponse
    {
        $data = $request->validated();
        $returnedData = [];


        foreach ($data['students'] as $student) {
            foreach ($student['class_sessions'] as $class_session) {
                $record = StudentAttendance::where('student_id', $student['id'])
                    ->where('class_session_id', $class_session['id'])->first();
                if ($record) {
                    $record->update([
                        'status' => $class_session['status'],
                        'created_by' => auth()->id(),
                    ]);
                } else {
                    $record = StudentAttendance::create([
                        'student_id' => $student['id'],
                        'class_session_id' => $class_session['id'],
                        'status' => $class_session['status'],
                        'created_by' => auth()->id(),
                    ]);
                }
                $returnedData[] = $record;
            }
        }

        return ResponseHelper::jsonResponse(
            $returnedData,
            'تم تحديث حضور الطلاب بنجاح'
        );
    }

    public function getDailyStudentsAttendance(ListDailyAttendanceRequest $request): JsonResponse
    {
        $data = $request->validated();
        $sessions = ClassSession::where('school_day_id', $data['school_day_id'])
            ->where('section_id', $data['section_id'])
            ->get();
        $students = Student::whereHas(
            'studentEnrollments',
            function ($query) use ($data) {
                $query->where('section_id', $data['section_id']);
            }
        )->get();

        $data = [];

        foreach ($students as $student) {
            $finalStatus = 'present';
            $totalCount = $sessions->count();
            $presentCount = 0;
            $absentCount = 0;
            $justifiedAbsentCount = 0;
            $lateCount = 0;
            foreach ($sessions as $classSession) {
                $status = StudentAttendance::where('student_id', $student->id)
                    ->where('class_session_id', $classSession->id)
                    ->first()?->status;

                if ($status === 'present') $presentCount++;
                if ($status === 'absent') $absentCount++;
                if ($status === 'justified_absent') $justifiedAbsentCount++;
                if ($status === 'lateness') $lateCount++;
            }
            $finalStatus = $this->determineDayStatusByCount($presentCount, $totalCount, $absentCount, $justifiedAbsentCount, $lateCount);
            $data[] = [
                'id' => $student->id,
                'full_name' => $student->user->first_name . ' ' . $student->user->last_name,
                'status' => $finalStatus,
            ];
        }


        return ResponseHelper::jsonResponse(
            $data,
            'تم عرض حضور الطلاب بنجاح'
        );
    }
    public function getSessionsStudentsAttendance(ListSessionsAttendanceRequest $request): JsonResponse
    {
        $data = $request->validated();
        $sessions = ClassSession::where('school_day_id', $data['school_day_id'])
            ->where('section_id', $data['section_id'])
            ->get();
        $students = Student::whereHas(
            'studentEnrollments',
            function ($query) use ($data) {
                $query->where('section_id', $data['section_id']);
            }
        )->get();
        $data = [];
        $totalCount = $sessions->count();
        $presentCount = 0;
        $absentCount = 0;
        $justifiedAbsentCount = 0;
        $lateCount = 0;
        foreach ($students as $student) {
            $presentCount = 0;
            $absentCount = 0;
            $justifiedAbsentCount = 0;
            $lateCount = 0;
            $sessionsData = [];
            foreach ($sessions as $classSession) {
                $status = StudentAttendance::where('student_id', $student->id)
                    ->where('class_session_id', $classSession->id)
                    ->first()?->status;
                if ($status === 'present') $presentCount++;
                if ($status === 'absent') $absentCount++;
                if ($status === 'justified_absent') $justifiedAbsentCount++;
                if ($status === 'lateness') $lateCount++;
                $sessionsData[] = [
                    'id' => $classSession->id,
                    'status' => $status,
                ];
            }
            $finalStatus = $this->determineDayStatusByCount($presentCount, $totalCount, $absentCount, $justifiedAbsentCount, $lateCount);
            $data[] = [
                'id' => $student->id,
                'full_name' => $student->user->first_name . ' ' . $student->user->last_name,
                'day_status' => $finalStatus,
                'class_sessions' => $sessionsData,
            ];
        }
        return ResponseHelper::jsonResponse(
            ['students' => $data],
            'تم عرض حضور الطلاب بنجاح'
        );
    }
    private function determineDayStatusByCount($presentCount, $totalCount, $absentCount, $justifiedAbsentCount, $lateCount): string
    {

        $sum = $presentCount + $absentCount + $justifiedAbsentCount + $lateCount;

        if ($sum != $totalCount) {
            return 'present';
        }
        return $presentCount == $totalCount ?
            'present' : ($absentCount + $justifiedAbsentCount == $totalCount ?
                ($justifiedAbsentCount > 0 ? 'justified_absent' : 'absent')
                : 'lateness');
    }
}
