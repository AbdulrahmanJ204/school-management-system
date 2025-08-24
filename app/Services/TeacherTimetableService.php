<?php

namespace App\Services;

use App\Models\Teacher;
use App\Models\Schedule;
use App\Models\TimeTable;
use App\Models\User;
use Carbon\Carbon;

class TeacherTimetableService
{
    /**
     * Get teacher timetable data
     *
     * @param int $userId
     * @return array
     */
    public function getTeacherTimetable(int $userId): array
    {
        $user = User::with(['teacher'])->findOrFail($userId);
        $teacher = $user->teacher;
        
        if (!$teacher) {
            throw new \Exception('المدرس غير موجود');
        }

        // Get weekly timetable
        $timetable = $this->getWeeklyTimetable($teacher);

        return [
            'timetable' => $timetable
        ];
    }

    /**
     * Get weekly timetable for teacher
     *
     * @param Teacher $teacher
     * @return array
     */
    private function getWeeklyTimetable(Teacher $teacher): array
    {
        // Get active timetable
        $activeTimetable = TimeTable::where('is_active', true)
            ->where('valid_from', '<=', now())
            ->where('valid_to', '>=', now())
            ->first();

        if (!$activeTimetable) {
            return [];
        }

        $dayNames = [
            1 => 'الأحد',
            2 => 'الإثنين', 
            3 => 'الثلاثاء',
            4 => 'الأربعاء',
            5 => 'الخميس',
            6 => 'الجمعة',
            7 => 'السبت'
        ];

        $timetable = [];

        // Get all days (1-7 for Sunday to Saturday)
        foreach ($dayNames as $dayNumber => $dayName) {
            $daySchedules = $this->getSchedulesForDay($teacher, $activeTimetable, $dayNumber);
            if (!empty($daySchedules)) {
                $timetable[] = [
                    'day_name' => $dayName,
                    'lectures' => $daySchedules
                ];
            }
        }

        return $timetable;
    }

    /**
     * Get schedules for a specific day
     *
     * @param Teacher $teacher
     * @param TimeTable $timetable
     * @param int $dayOfWeek
     * @return array
     */
    private function getSchedulesForDay(Teacher $teacher, TimeTable $timetable, int $dayOfWeek): array
    {
        $schedules = Schedule::where('timetable_id', $timetable->id)
            ->where('week_day', $dayOfWeek)
            ->whereHas('teacherSectionSubject', function ($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
            ->with([
                'classPeriod',
                'teacherSectionSubject.teacher.user',
                'teacherSectionSubject.subject',
                'teacherSectionSubject.section',
                'teacherSectionSubject.grade'
            ])
            ->orderBy('class_period_id')
            ->get();

        $lectures = [];
        foreach ($schedules as $schedule) {
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

        return $lectures;
    }
}
