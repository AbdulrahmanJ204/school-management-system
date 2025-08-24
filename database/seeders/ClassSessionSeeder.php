<?php

namespace Database\Seeders;

use App\Models\ClassSession;
use App\Models\Schedule;
use App\Models\SchoolDay;
use App\Models\Teacher;
use App\Models\Subject;
use App\Models\Section;
use App\Models\ClassPeriod;
use Illuminate\Database\Seeder;

class ClassSessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schedules = Schedule::all();
        // Get recent school days (last 20 school days from the available data)
        $schoolDays = SchoolDay::orderBy('date', 'desc')->limit(20)->get();
        $teachers = Teacher::all();
        $subjects = Subject::all();
        $sections = Section::all();
        $classPeriods = ClassPeriod::all();

        if ($schedules->isEmpty() || $schoolDays->isEmpty() || $teachers->isEmpty() || 
            $subjects->isEmpty() || $sections->isEmpty() || $classPeriods->isEmpty()) {
            return;
        }

        foreach ($schoolDays as $schoolDay) {
            // Create 2-5 class sessions per school day
            $sessionsCount = rand(2, 5);
            
            for ($i = 0; $i < $sessionsCount; $i++) {
                $schedule = $schedules->random();
                $teacher = $teachers->random();
                $subject = $subjects->random();
                $section = $sections->random();
                $classPeriod = $classPeriods->random();

                // Check if session already exists for this schedule and school day
                $existingSession = ClassSession::where('schedule_id', $schedule->id)
                    ->where('school_day_id', $schoolDay->id)
                    ->first();

                if (!$existingSession) {
                    ClassSession::create([
                        'schedule_id' => $schedule->id,
                        'school_day_id' => $schoolDay->id,
                        'teacher_id' => $teacher->id,
                        'subject_id' => $subject->id,
                        'section_id' => $section->id,
                        'class_period_id' => $classPeriod->id,
                        'status' => $this->getRandomStatus($schoolDay->date),
                        'total_students_count' => rand(20, 35),
                        'present_students_count' => rand(15, 30),
                    ]);
                }
            }
        }
    }

    /**
     * Get random status based on date
     */
    private function getRandomStatus($date): string
    {
        $today = now()->toDateString();
        $dateString = $date->toDateString();

        if ($dateString < $today) {
            // Past dates - mostly completed
            return rand(1, 10) <= 8 ? 'completed' : 'scheduled';
        } elseif ($dateString === $today) {
            // Today - mix of statuses
            $rand = rand(1, 10);
            if ($rand <= 3) return 'scheduled';
            else return 'completed';
        } else {
            // Future dates - mostly scheduled
            return rand(1, 10) <= 9 ? 'scheduled' : 'completed';
        }
    }
}
