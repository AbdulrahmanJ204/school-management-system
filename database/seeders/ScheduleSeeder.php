<?php

namespace Database\Seeders;

use App\Models\Schedule;
use App\Models\TimeTable;
use App\Models\ClassPeriod;
use App\Models\TeacherSectionSubject;
use App\Enums\WeekDay;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $timetables = TimeTable::all();
        $classPeriods = ClassPeriod::where('type', \App\Enums\ClassPeriodType::STUDY)->get();
        $teacherSectionSubjects = TeacherSectionSubject::all();

        if ($timetables->isEmpty()) {
            // Create a default timetable if none exists
            $timetable = TimeTable::create([
                'valid_from' => now()->startOfYear(),
                'valid_to' => now()->endOfYear(),
                'is_active' => true,
                'created_by' => 1,
            ]);
        } else {
            $timetable = $timetables->first();
        }

        if ($classPeriods->isEmpty() || $teacherSectionSubjects->isEmpty()) {
            return;
        }

        // Create schedules for each day of the week
        $weekDays = [WeekDay::SUNDAY, WeekDay::MONDAY, WeekDay::TUESDAY, WeekDay::WEDNESDAY, WeekDay::THURSDAY];

        foreach ($weekDays as $weekDay) {
            // Create 500 schedules per day
            $schedulesCount = 500;
            
            for ($i = 0; $i < $schedulesCount; $i++) {
                $classPeriod = $classPeriods->random();
                $teacherSectionSubject = $teacherSectionSubjects->random();

                // Check if schedule already exists for this combination
                $existingSchedule = Schedule::where('timetable_id', $timetable->id)
                    ->where('week_day', $weekDay)
                    ->where('class_period_id', $classPeriod->id)
                    ->first();

                if (!$existingSchedule) {
                    Schedule::create([
                        'timetable_id' => $timetable->id,
                        'class_period_id' => $classPeriod->id,
                        'teacher_section_subject_id' => $teacherSectionSubject->id,
                        'week_day' => $weekDay,
                        'created_by' => 1,
                    ]);
                }
            }
        }
    }
}
