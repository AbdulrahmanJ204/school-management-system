<?php

namespace App\Jobs;

use App\Models\ClassSession;
use App\Models\Schedule;
use App\Models\StudentEnrollment;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class RegenerateClassSessions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $scheduleId;
    protected $schoolDayId;

    public function __construct($scheduleId, $schoolDayId)
    {
        $this->scheduleId  = $scheduleId;
        $this->schoolDayId = $schoolDayId;
    }

    public function handle()
    {
        $schedule = Schedule::with(['classPeriod', 'timetable', 'teacherSectionSubject'])
            ->findOrFail($this->scheduleId);

        if (!$schedule->timetable->is_active) {
            return;
        }

        $startDate = Carbon::now()->addWeeks(4)->startOfWeek(Carbon::SUNDAY); // generate next month

        DB::transaction(function () use ($schedule, $startDate) {
            for ($week = 0; $week < 4; $week++) {
                for ($day = 0; $day < 5; $day++) {
                    $dayDate = $startDate->copy()->addWeeks($week)->addDays($day);

                    ClassSession::updateOrCreate([
                        'schedule_id' => $schedule->id,
                        'date'        => $dayDate->toDateString(),
                    ], [
                        'school_day_id'   => $this->schoolDayId,
                        'teacher_id'      => $schedule->teacherSectionSubject->teacher_id,
                        'section_id'      => $schedule->teacherSectionSubject->section_id,
                        'subject_id'      => $schedule->teacherSectionSubject->subject_id,
                        'class_period_id' => $schedule->class_period_id,
                        'status'          => 'scheduled',
                        'total_students'  => StudentEnrollment::where('semester_id', $schedule->timetable->semester_id)
                            ->where('section_id', $schedule->teacherSectionSubject->section_id)
                            ->count(),
                    ]);
                }
            }
        });
    }
}
