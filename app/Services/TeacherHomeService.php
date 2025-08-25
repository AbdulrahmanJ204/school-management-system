<?php

namespace App\Services;

use App\Enums\WeekDay;
use App\Models\Teacher;
use App\Models\Schedule;
use App\Models\TimeTable;
use App\Models\User;
use Carbon\Carbon;
use Exception;

class TeacherHomeService
{
    /**
     * Get teacher home data
     *
     * @param int $userId
     * @return array
     * @throws Exception
     */
    public function getTeacherHomeData(int $userId): array
    {
        $user = User::with(['teacher'])->findOrFail($userId);
        $teacher = $user->teacher;

        if (!$teacher) {
            throw new Exception('Teacher not found');
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
//        $tomorrow = Carbon::now()->addDay();

        // Convert Carbon dayOfWeek to WeekDay enum
        $carbonDayOfWeek = $today->dayOfWeek; // 0=Sunday, 1=Monday, ..., 6=Saturday
        $weekDayMapping = [
            0 => WeekDay::SUNDAY->value,    // Sunday
            1 => WeekDay::MONDAY->value,    // Monday
            2 => WeekDay::TUESDAY->value,   // Tuesday
            3 => WeekDay::WEDNESDAY->value, // Wednesday
            4 => WeekDay::THURSDAY->value,  // Thursday
            5 => WeekDay::FRIDAY->value,    // Friday
            6 => WeekDay::SATURDAY->value,  // Saturday
        ];

        $todayWeekDay = $weekDayMapping[$carbonDayOfWeek];

        $dayNames = [
            WeekDay::SUNDAY->value => 'الأحد',
            WeekDay::MONDAY->value => 'الإثنين',
            WeekDay::TUESDAY->value => 'الثلاثاء',
            WeekDay::WEDNESDAY->value => 'الأربعاء',
            WeekDay::THURSDAY->value => 'الخميس',
            WeekDay::FRIDAY->value => 'الجمعة',
            WeekDay::SATURDAY->value => 'السبت'
        ];

        $result = [];

        // Get today's schedule
        if ($todayWeekDay) {
            $todaySchedules = $this->getSchedulesForDay($teacher, $activeTimetable, $todayWeekDay);
            if (!empty($todaySchedules)) {
                $result[] = [
                    'day_name' => $dayNames[$todayWeekDay],
                    'lectures' => $todaySchedules
                ];
            }
        }
//
//        // Get tomorrow's schedule (if it's a different day)
//        if ($tomorrowDayOfWeek && $tomorrowDayOfWeek !== $todayDayOfWeek) {
//            $tomorrowSchedules = $this->getSchedulesForDay($teacher, $activeTimetable, $tomorrowDayOfWeek);
//            if (!empty($tomorrowSchedules)) {
//                $result[] = [
//                    'day_name' => $dayNames[$tomorrowDayOfWeek],
//                    'lectures' => $tomorrowSchedules
//                ];
//            }
//        }

        return $result;
    }



    /**
     * Get schedules for a specific day
     *
     * @param Teacher $teacher
     * @param TimeTable $timetable
     * @param string $dayOfWeek
     * @return array
     */
    private function getSchedulesForDay(Teacher $teacher, TimeTable $timetable, string $dayOfWeek): array
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
                'teacherSectionSubject.grade',
                'classSessions' => function ($query) {
                    $query->today()->with(['schoolDay']);
                }
            ])
            ->orderBy('class_period_id')
            ->get();

        $lectures = [];
        foreach ($schedules as $schedule) {
            // Get today's class session for this schedule (if exists)
            $todayClassSession = $schedule->classSessions->first();


            // $today = Carbon::today()->format('Y-m-d');
            // $todayClassSession = $schedule->classSessions->first(function ($session) use ($today) {
            //     return $session->schoolDay && $session->schoolDay->date->format('Y-m-d') === $today;
            // });

            $lectures[] = [
                'id' => $schedule->id,
                'subject_name' => $schedule->teacherSectionSubject->subject->name,
                'teacher_name' => $schedule->teacherSectionSubject->teacher->user->first_name . ' ' . $schedule->teacherSectionSubject->teacher->user->last_name,
                'start_time' => Carbon::parse($schedule->classPeriod->start_time)->format('H:i'),
                'end_time' => Carbon::parse($schedule->classPeriod->end_time)->format('H:i'),
                'section' => $schedule->teacherSectionSubject->section->title,
                'grade' => $schedule->teacherSectionSubject->grade->title,
                'class_session' => $todayClassSession ? [
                    'id' => $todayClassSession->id,
                    'status' => $todayClassSession->status,
                    'total_students_count' => $todayClassSession->total_students_count,
                    'present_students_count' => $todayClassSession->present_students_count,
                    'school_day' => [
                        'id' => $todayClassSession->schoolDay->id,
                        'date' => $todayClassSession->schoolDay->date->format('Y-m-d'),
                        'type' => $todayClassSession->schoolDay->type
                    ]
                ] : null
            ];
        }

        return $lectures;
    }
}
