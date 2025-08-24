<?php

namespace App\Services;

use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\Schedule;
use App\Models\TimeTable;
use App\Models\User;
use Carbon\Carbon;

class StudentTimetableService
{
    /**
     * Get student timetable data
     *
     * @param int $userId
     * @return array
     */
    public function getStudentTimetable(int $userId): array
    {
        $user = User::with(['student'])->findOrFail($userId);
        $student = $user->student;
        
        if (!$student) {
            throw new \Exception('الطالب غير موجود');
        }

        // Get current enrollment
        $currentEnrollment = $this->getCurrentEnrollment($student);
        
        if (!$currentEnrollment) {
            throw new \Exception('الطالب ليس مسجل في أي شعبة');
        }

        // Get weekly timetable
        $timetable = $this->getWeeklyTimetable($currentEnrollment);

        return [
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
            1 => 'الأحد',
            2 => 'الإثنين',
            3 => 'الثلاثاء',
            4 => 'الأربعاء',
            5 => 'الخميس',
            6 => 'الجمعة',
            7 => 'السبت'
        ];

        $timetable = [];

        foreach ($weekDays as $dayNumber => $dayName) {
            $daySchedules = $schedules->where('week_day', $dayNumber);
            
            $lectures = [];
            foreach ($daySchedules as $schedule) {
                $lectures[] = [
                    'id' => $schedule->id,
                    'subject_name' => $schedule->teacherSectionSubject->subject->name,
                    'teacher_name' => $schedule->teacherSectionSubject->teacher->user->first_name . ' ' . $schedule->teacherSectionSubject->teacher->user->last_name,
                    'start_time' => Carbon::parse($schedule->classPeriod->start_time)->format('H:i'),
                    'end_time' => Carbon::parse($schedule->classPeriod->end_time)->format('H:i'),
                    'section_name' => $schedule->teacherSectionSubject->section->title,
                    'grade_name' => $schedule->teacherSectionSubject->grade->title
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
