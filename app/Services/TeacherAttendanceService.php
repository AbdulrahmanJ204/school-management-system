<?php

namespace App\Services;

use App\Enums\PermissionEnum;
use App\Exceptions\PermissionException;
use App\Helpers\ResponseHelper;
use App\Http\Resources\TeacherAttendanceResource;
use App\Models\ClassSession;
use App\Models\SchoolDay;
use App\Models\Semester;
use App\Models\Teacher;
use App\Models\TeacherAttendance;
use App\Models\Year;
use App\Traits\HasPermissionChecks;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class TeacherAttendanceService
{
    use HasPermissionChecks;

    /**
     * @throws PermissionException
     */
    public function listTeacherAttendances(Request $request): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_TEACHER_ATTENDANCES);

        $query = TeacherAttendance::with([
            'teacher.user',
            'classSession',
            'createdBy'
        ]);

        // Apply filters
        if ($request->has('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
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

        $teacherAttendances = $query->orderBy('created_at', 'desc')->paginate(15);

        return ResponseHelper::jsonResponse(
            TeacherAttendanceResource::collection($teacherAttendances),
            'تم عرض حضور المعلمين بنجاح'
        );
    }

    /**
     * @throws PermissionException
     */
    public function createTeacherAttendance(Request $request): JsonResponse
    {
        $this->checkPermission(PermissionEnum::CREATE_TEACHER_ATTENDANCE);

        $data = $request->validated();
        $data['created_by'] = auth()->id();

        $teacherAttendance = TeacherAttendance::create($data);

        return ResponseHelper::jsonResponse(
            new TeacherAttendanceResource($teacherAttendance->load([
                'teacher.user',
                'classSession',
                'createdBy'
            ])),
            'تم إضافة حضور المعلم بنجاح',
            ResponseAlias::HTTP_CREATED,
        );
    }

    /**
     * @throws PermissionException
     */
    public function showTeacherAttendance($id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_TEACHER_ATTENDANCE);

        $teacherAttendance = TeacherAttendance::with([
            'teacher.user',
            'classSession',
            'createdBy'
        ])
            ->findOrFail($id);

        return ResponseHelper::jsonResponse(
            new TeacherAttendanceResource($teacherAttendance),
            'تم عرض حضور المعلم بنجاح'
        );
    }

    /**
     * @throws PermissionException
     */
    public function updateTeacherAttendance(Request $request, $id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::UPDATE_TEACHER_ATTENDANCE);

        $teacherAttendance = TeacherAttendance::findOrFail($id);
        $data = $request->validated();

        $teacherAttendance->update($data);

        return ResponseHelper::jsonResponse(
            new TeacherAttendanceResource($teacherAttendance->load([
                'teacher.user',
                'classSession',
                'createdBy'
            ])),
            'تم تحديث حضور المعلم بنجاح'
        );
    }

    /**
     * @throws PermissionException
     */
    public function deleteTeacherAttendance($id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::DELETE_TEACHER_ATTENDANCE);

        $teacherAttendance = TeacherAttendance::findOrFail($id);
        $teacherAttendance->delete();

        return ResponseHelper::jsonResponse(
            null,
            'تم حذف حضور المعلم بنجاح'
        );
    }

    /**
     * @throws PermissionException
     */
    public function getByTeacher($teacherId): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_TEACHER_ATTENDANCES);

        $teacherAttendances = TeacherAttendance::where('teacher_id', $teacherId)
            ->with([
                'teacher.user',
                'classSession',
                'createdBy'
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return ResponseHelper::jsonResponse(
            TeacherAttendanceResource::collection($teacherAttendances),
            'تم عرض حضور المعلم بنجاح'
        );
    }

    /**
     * @throws PermissionException
     */
    public function getByClassSession($classSessionId): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_TEACHER_ATTENDANCES);

        $teacherAttendances = TeacherAttendance::where('class_session_id', $classSessionId)
            ->with([
                'teacher.user',
                'classSession',
                'createdBy'
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return ResponseHelper::jsonResponse(
            TeacherAttendanceResource::collection($teacherAttendances),
            'تم عرض حضور المعلمين في الجلسة بنجاح'
        );
    }

    /**
     * Generate detailed attendance report for a teacher for all months in current year
     * @throws PermissionException
     */
    public function generateAttendanceReport($request): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_TEACHER_ATTENDANCES);

        // Get teacher_id from authenticated user
        $user = auth()->user();
        $teacher = Teacher::where('user_id', $user->id)->first();

        if (!$teacher) {
            return ResponseHelper::jsonResponse(
                ['months' => []],
                'لم يتم العثور على بيانات المعلم',
                ResponseAlias::HTTP_NOT_FOUND,
                false
            );
        }

        $teacherId = $teacher->id;

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
            $semesterMonths = $this->generateSemesterMonthsData($teacherId, $semester);
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
    private function generateSemesterMonthsData($teacherId, $semester): ?array
    {
        // Get all school days for the specified semester
        $schoolDays = SchoolDay::where('semester_id', $semester->id)
            ->with(['semester'])
            ->orderBy('date')
            ->get();

        if ($schoolDays->isEmpty()) {
            return null; // No school days in this semester
        }

        $classSessions = ClassSession::where('teacher_id', $teacherId)
            ->whereHas('schoolDay', function ($query) use ($semester) {
                $query->where('semester_id', $semester->id);
            })
            ->with(['schoolDay', 'teacher.user', 'subject', 'classPeriod'])
            ->orderBy('created_at')
            ->get();

        // Get teacher attendances for this semester
        $teacherAttendances = TeacherAttendance::where('teacher_id', $teacherId)
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

            // Filter teacher attendances for this month
            $monthTeacherAttendances = $teacherAttendances->filter(function ($attendance) use ($year, $month) {
                return $attendance->classSession->schoolDay->date->year === $year && $attendance->classSession->schoolDay->date->month === $month;
            });

            // Build month data
            $monthData = $this->buildMonthData($monthSchoolDays, $monthClassSessions, $monthTeacherAttendances, $year, $month);
            if ($monthData) {
                $monthsData[] = $monthData;
            }
        }

        return $monthsData;
    }

    /**
     * Build month data with statistics and daily details
     */
    private function buildMonthData($schoolDays, $classSessions, $teacherAttendances, $year, $month): array
    {
        $stats = $this->calculateMonthStats($schoolDays, $classSessions, $teacherAttendances);
        $days = $this->buildDaysData($schoolDays, $classSessions, $teacherAttendances);

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
    private function calculateMonthStats($schoolDays, $classSessions, $teacherAttendances): array
    {
        $totalDays = $schoolDays->where('type', 'study')->count();
        $totalSessions = $classSessions->count();

        // Count explicit attendance records
        $presentSessions = $teacherAttendances->where('status', 'present')->count();
        $absentSessions = $teacherAttendances->where('status', 'absent')->count();
        $justifiedAbsentSessions = $teacherAttendances->where('status', 'justified_absent')->count();
        $lateSessions = $teacherAttendances->where('status', 'lateness')->count();

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
            'presentDays' => $this->countPresentDays($schoolDays, $teacherAttendances, $classSessions),
            'absentDays' => $this->countAbsentDays($schoolDays, $teacherAttendances),
            'justifiedAbsentDays' => $this->countJustifiedAbsentDays($schoolDays, $teacherAttendances),
            'lateDays' => $this->countLateDays($schoolDays, $teacherAttendances)
        ];
    }

    /**
     * Build daily attendance data
     */
    private function buildDaysData($schoolDays, $classSessions, $teacherAttendances): array
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
                $sessions = $this->buildSessionsData($daySessions, $teacherAttendances, $date);
                $dayStatus = $this->determineDayStatus($daySessions, $teacherAttendances, $date);

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
    private function buildSessionsData($daySessions, $teacherAttendances, $date): array
    {
        $sessions = [];

        foreach ($daySessions as $session) {
            $attendanceKey = $date . '_' . $session->classPeriod->id;
            $attendance = $teacherAttendances->get($attendanceKey);

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
    private function determineDayStatus($daySessions, $teacherAttendances, $date): string
    {
        if ($daySessions->isEmpty()) {
            return 'notOccurredYet';
        }

        $hasPresent = false;
        $hasAbsent = false;
        $hasLate = false;
        $sessionsWithRecords = 0;

        foreach ($daySessions as $session) {
            $attendanceKey = $date . '_' . $session->classPeriod->id;
            $attendance = $teacherAttendances->get($attendanceKey);
            $presentCount = 0;
            $absentCount = 0;
            $justifiedAbsentCount = 0;
            $lateCount = 0;
            $totalCount = 0;
            if ($attendance) {
                $totalCount++;
                switch ($attendance->status) {
                    case 'present':
                        $presentCount++;
                        break;
                    case 'absent':
                        $absentCount++;
                        break;
                    case 'justified_absent':
                        $justifiedAbsentCount++;
                        break;
                    case 'lateness':
                        $lateCount++;
                        break;
                }
            }
        }

        return $this->determineDayStatusByCount($presentCount, $totalCount, $absentCount, $justifiedAbsentCount, $lateCount);
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
    private function countPresentDays($schoolDays, $teacherAttendances, $classSessions): int
    {
        $presentDays = 0;

        foreach ($schoolDays->where('type', 'study') as $schoolDay) {
            $date = $schoolDay->date->format('Y-m-d');
            $daySessions = $classSessions->where('school_day_id', $schoolDay->id);
            $dayAttendances = $teacherAttendances->filter(function ($attendance) use ($date) {
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
    private function countAbsentDays($schoolDays, $teacherAttendances)
    {
        $absentDays = 0;

        foreach ($schoolDays->where('type', 'study') as $schoolDay) {
            $date = $schoolDay->date->format('Y-m-d');
            $dayAttendances = $teacherAttendances->filter(function ($attendance) use ($date) {
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
    private function countJustifiedAbsentDays($schoolDays, $teacherAttendances)
    {
        $justifiedAbsentDays = 0;

        foreach ($schoolDays->where('type', 'study') as $schoolDay) {
            $date = $schoolDay->date->format('Y-m-d');
            $dayAttendances = $teacherAttendances->filter(function ($attendance) use ($date) {
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
    private function countLateDays($schoolDays, $teacherAttendances)
    {
        $lateDays = 0;

        foreach ($schoolDays->where('type', 'study') as $schoolDay) {
            $date = $schoolDay->date->format('Y-m-d');
            $dayAttendances = $teacherAttendances->filter(function ($attendance) use ($date) {
                return $attendance->classSession->schoolDay->date->format('Y-m-d') === $date;
            });

            if ($dayAttendances->where('status', 'lateness')->count() > 0) {
                $lateDays++;
            }
        }

        return $lateDays;
    }
}
