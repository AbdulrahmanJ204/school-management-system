<?php

namespace App\Console\Commands;

use App\Models\ClassSession;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateClassSessions extends Command
{
    protected $signature = 'class_session:generate {schedule_id} {school_day_id}';
    protected $description = 'Generate class sessions for the next 4 weeks for a given schedule and school day';

    public function handle()
    {
        $scheduleId   = $this->argument('schedule_id');
        $schoolDayId  = $this->argument('school_day_id');

        $schedule = Schedule::with(['classPeriod', 'timetable'])
            ->findOrFail($scheduleId);

        // 1. Validate that timetable is active
        if (!$schedule->timetable->is_active) {
            $this->error('Timetable is not active for this schedule.');
            return 1;
        }

        // 2. Generate sessions for 4 weeks
        DB::transaction(function () use ($schedule, $schoolDayId) {
            $startDate = Carbon::now()->startOfWeek(Carbon::SUNDAY); // Sunday start
            $weeks     = 4;

            for ($week = 0; $week < $weeks; $week++) {
                for ($day = 0; $day < 5; $day++) { // Sunday → Thursday
                    $dayDate = $startDate->copy()->addWeeks($week)->addDays($day);

                    // 5 lectures per day (based on schedule/class periods)
                    // Here we assume schedule defines a single period → session
                    // You can expand if multiple
                    ClassSession::create([
                        'schedule_id'   => $schedule->id,
                        'school_day_id' => $schoolDayId,
                        'teacher_id'    => $schedule->teacherSectionSubject->teacher_id,
                        'section_id'    => $schedule->teacherSectionSubject->section_id,
                        'subject_id'    => $schedule->teacherSectionSubject->subject_id,
                        'class_period_id' => $schedule->class_period_id,
                        'date'          => $dayDate->toDateString(),
                        'status'        => 'scheduled',
                        'total_students'=> $this->getTotalStudents($schedule, $schoolDayId),
                    ]);
                }
            }
        });

        $this->info('Class sessions generated successfully.');
        return 0;
    }

    private function getTotalStudents($schedule, $schoolDayId)
    {
        $schoolDay = \App\Models\SchoolDay::findOrFail($schoolDayId);

        return \App\Models\StudentEnrollment::where('semester_id', $schoolDay->semester_id)
            ->where('section_id', $schedule->teacherSectionSubject->section_id)
            ->count();
    }
}
