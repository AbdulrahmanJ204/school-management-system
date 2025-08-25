<?php

namespace App\Services;

use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\StudentMark;
use App\Models\StudentAttendance;
use App\Models\Schedule;
use App\Models\TimeTable;
use App\Models\ClassSession;
use App\Models\User;
use App\Enums\WeekDay;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;

class StudentHomeService
{
    /**
     * Get student home data
     *
     * @param int $userId
     * @return array
     * @throws Exception
     */
    public function getStudentHomeData(int $userId): array
    {
        $user = User::with(['student'])->findOrFail($userId);
        $student = $user->student;

        if (!$student) {
            throw new Exception('Student not found');
        }

        // Get current enrollment
        $currentEnrollment = $this->getCurrentEnrollment($student);

        if (!$currentEnrollment) {
            throw new Exception('No active enrollment found for student');
        }

        // Get user basic info
        $userInfo = $this->getUserInfo($user, $currentEnrollment);

        // Get weekly timetable
        $timetable = $this->getWeeklyTimetable($currentEnrollment);

        return [
            'user' => $userInfo,
            'timetable' => $timetable
        ];
    }

    /**
     * Get current student enrollment
     *
     * @param Student $student
     * @return StudentEnrollment|null
     */
    private function getCurrentEnrollment(Student $student): ?StudentEnrollment
    {
        // Get the most recent enrollment (current academic year/semester)
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
     * Get user basic information
     *
     * @param User $user
     * @param StudentEnrollment $enrollment
     * @return array
     */
    private function getUserInfo(User $user, StudentEnrollment $enrollment): array
    {
        $academicAverage = $this->calculateAcademicAverage($enrollment);
        $attendanceAverage = $this->calculateAttendanceAverage($enrollment);

        return [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'photo' => $user->image ? url('storage/' . $user->image) : null,
            'grade_name' => $enrollment->section->grade->title,
            'section_name' => $enrollment->section->title,
            'academic_average' => round($academicAverage, 2),
            'average_presence' => round($attendanceAverage, 2)
        ];
    }

    /**
     * Calculate academic average (quiz + exam scores / max possible scores)
     *
     * @param StudentEnrollment $enrollment
     * @return float
     */
    private function calculateAcademicAverage(StudentEnrollment $enrollment): float
    {
        $studentMarks = StudentMark::where('enrollment_id', $enrollment->id)
            ->with('subject')
            ->get();

        if ($studentMarks->isEmpty()) {
            return 0.0;
        }

        $totalScore = 0;
        $totalMaxScore = 0;

        foreach ($studentMarks as $mark) {
            // Calculate actual score (quiz + exam)
            $actualScore = ($mark->quiz ?? 0) + ($mark->exam ?? 0);

            // Calculate max possible score from subject percentages
            $subject = $mark->subject;
            $maxQuizScore = ($subject->full_mark * $subject->quiz_percentage) / 100;
            $maxExamScore = ($subject->full_mark * $subject->exam_percentage) / 100;
            $maxScore = $maxQuizScore + $maxExamScore;

            $totalScore += $actualScore;
            $totalMaxScore += $maxScore;
        }

        if ($totalMaxScore == 0) {
            return 0.0;
        }

        return ($totalScore / $totalMaxScore) * 100;
    }

    /**
     * Calculate attendance average (present + late days / total days)
     *
     * @param StudentEnrollment $enrollment
     * @return float
     */
    private function calculateAttendanceAverage(StudentEnrollment $enrollment): float
    {
        $attendanceRecords = StudentAttendance::where('student_id', $enrollment->student_id)
            ->whereHas('classSession', function ($query) use ($enrollment) {
                $query->where('section_id', $enrollment->section_id);
            })
            ->get();

        if ($attendanceRecords->isEmpty()) {
            return 100.0; // Default to 100% if no attendance records
        }

        $totalDays = $attendanceRecords->count();
        $presentDays = $attendanceRecords->whereIn('status', ['present', 'late'])->count();

        return ($presentDays / $totalDays) * 100;
    }

    /**
     * Get weekly timetable for student
     *
     * @param StudentEnrollment $enrollment
     * @return array
     */
    private function getWeeklyTimetable(StudentEnrollment $enrollment): array
    {
        // Get active timetable
        $activeTimetable = TimeTable::where('is_active', true)
            ->where('valid_from', '<=', now())
            ->where('valid_to', '>=', now())
            ->first();

        if (!$activeTimetable) {
            return [];
        }

        // Get schedules for the student's section
        $schedules = Schedule::where('timetable_id', $activeTimetable->id)
            ->whereHas('teacherSectionSubject', function ($query) use ($enrollment) {
                $query->where('section_id', $enrollment->section_id)
                      ->where('grade_id', $enrollment->section->grade_id);
            })
            ->with([
                'classPeriod',
                'teacherSectionSubject.teacher.user',
                'teacherSectionSubject.subject',
                'teacherSectionSubject.section',
                'teacherSectionSubject.grade'
            ])
            ->orderBy('week_day')
            ->orderBy('class_period_id')
            ->get();

        // Group schedules by day
        $weekDays = [
            WeekDay::SUNDAY->value => 'الأحد',
            WeekDay::MONDAY->value => 'الإثنين',
            WeekDay::TUESDAY->value => 'الثلاثاء',
            WeekDay::WEDNESDAY->value => 'الأربعاء',
            WeekDay::THURSDAY->value => 'الخميس',
            WeekDay::FRIDAY->value => 'الجمعة',
            WeekDay::SATURDAY->value => 'السبت'
        ];

        $timetable = [];

        foreach ($weekDays as $dayValue => $dayName) {
            $daySchedules = $schedules->where('week_day', $dayValue);

            $lectures = [];
            foreach ($daySchedules as $schedule) {
                $lectures[] = [
                    'id' => $schedule->id,
                    'subject_name' => $schedule->teacherSectionSubject->subject->name,
                    'teacher_name' => $schedule->teacherSectionSubject->teacher->user->first_name . ' ' . $schedule->teacherSectionSubject->teacher->user->last_name,
                    'start_time' => Carbon::parse($schedule->classPeriod->start_time)->format('H:i'),
                    'end_time' => Carbon::parse($schedule->classPeriod->end_time)->format('H:i'),
                    'section' => $schedule->teacherSectionSubject->section->title,
                    'grade' => $schedule->teacherSectionSubject->grade->title
                ];
            }

            if (!empty($lectures)) {
                $timetable[] = [
                    'day_name' => $dayName,
                    'lectures' => $lectures
                ];
            }
        }

        return $timetable;
    }
}
