<?php

namespace App\Services;

use App\Models\Teacher;
use App\Models\Schedule;
use App\Models\TimeTable;
use App\Models\User;
use Carbon\Carbon;

class TeacherHomeService
{
    /**
     * Get teacher home data
     *
     * @param int $userId
     * @return array
     */
    public function getTeacherHomeData(int $userId): array
    {
        $user = User::with(['teacher'])->findOrFail($userId);
        $teacher = $user->teacher;
        
        if (!$teacher) {
            throw new \Exception('Teacher not found');
        }

        // Get user basic info
        $userInfo = $this->getUserInfo($user);
        
        // Get today's lectures
        $todayLectures = $this->getTodayLectures($teacher);

        return [
            'user' => $userInfo,
            'today_lectures' => $todayLectures
        ];
    }

    /**
     * Get user basic information
     *
     * @param User $user
     * @return array
     */
    private function getUserInfo(User $user): array
    {
        return [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'photo' => $user->image ? url('storage/' . $user->image) : null,
        ];
    }

    /**
     * Get today's lectures for teacher (today and tomorrow)
     *
     * @param Teacher $teacher
     * @return array
     */
    private function getTodayLectures(Teacher $teacher): array
    {
        // Get active timetable
        $activeTimetable = TimeTable::where('is_active', true)
            ->where('valid_from', '<=', now())
            ->where('valid_to', '>=', now())
            ->first();

        if (!$activeTimetable) {
            return [];
        }

        // Get current day of week (1=Sunday, 7=Saturday)
        $today = Carbon::now();
        $tomorrow = Carbon::now()->addDay();
        
        $todayDayOfWeek = $this->getDayOfWeek($today);
        $tomorrowDayOfWeek = $this->getDayOfWeek($tomorrow);

        $dayNames = [
            1 => 'الأحد',
            2 => 'الإثنين', 
            3 => 'الثلاثاء',
            4 => 'الأربعاء',
            5 => 'الخميس',
            6 => 'الجمعة',
            7 => 'السبت'
        ];

        $result = [];

        // Get today's schedule
        if ($todayDayOfWeek) {
            $todaySchedules = $this->getSchedulesForDay($teacher, $activeTimetable, $todayDayOfWeek);
            if (!empty($todaySchedules)) {
                $result[] = [
                    'day_name' => $dayNames[$todayDayOfWeek],
                    'lectures' => $todaySchedules
                ];
            }
        }

        // Get tomorrow's schedule (if it's a different day)
        if ($tomorrowDayOfWeek && $tomorrowDayOfWeek !== $todayDayOfWeek) {
            $tomorrowSchedules = $this->getSchedulesForDay($teacher, $activeTimetable, $tomorrowDayOfWeek);
            if (!empty($tomorrowSchedules)) {
                $result[] = [
                    'day_name' => $dayNames[$tomorrowDayOfWeek],
                    'lectures' => $tomorrowSchedules
                ];
            }
        }

        return $result;
    }

    /**
     * Get day of week number (1=Sunday, 7=Saturday)
     *
     * @param Carbon $date
     * @return int
     */
    private function getDayOfWeek(Carbon $date): int
    {
        // Carbon's dayOfWeek: 0=Sunday, 1=Monday, ..., 6=Saturday
        // We need: 1=Sunday, 2=Monday, ..., 7=Saturday
        return $date->dayOfWeek + 1;
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
                'section' => $schedule->teacherSectionSubject->section->title,
                'grade' => $schedule->teacherSectionSubject->grade->title
            ];
        }

        return $lectures;
    }
}
