<?php

namespace Database\Seeders;

use App\Models\ClassSession;
use App\Models\Schedule;
use App\Models\SchoolDay;
use App\Models\Teacher;
use App\Models\Subject;
use App\Models\Section;
use App\Models\ClassPeriod;
use App\Enums\WeekDay;
use Illuminate\Database\Seeder;
use App\Enums\ClassSessionStatus;

class ClassSessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schedules = Schedule::with(['teacherSectionSubject.teacher', 'teacherSectionSubject.subject', 'teacherSectionSubject.section', 'classPeriod'])->get();
        $schoolDays = SchoolDay::all();

        if ($schedules->isEmpty() || $schoolDays->isEmpty()) {
            $this->command->info("Skipping ClassSessionSeeder: Schedules count: {$schedules->count()}, SchoolDays count: {$schoolDays->count()}");
            return;
        }

        $this->command->info("Creating class sessions for ALL schedules on ALL matching school days...");
        $this->command->info("Total schedules: {$schedules->count()}, Total school days: {$schoolDays->count()}");

        $createdCount = 0;
        $skippedCount = 0;

        foreach ($schoolDays as $schoolDay) {
            // Convert Carbon dayOfWeek to WeekDay enum
            $carbonDayOfWeek = $schoolDay->date->dayOfWeek; // 0=Sunday, 1=Monday, ..., 6=Saturday
            $weekDayMapping = [
                0 => WeekDay::SUNDAY->value,    // Sunday
                1 => WeekDay::MONDAY->value,    // Monday 
                2 => WeekDay::TUESDAY->value,   // Tuesday
                3 => WeekDay::WEDNESDAY->value, // Wednesday
                4 => WeekDay::THURSDAY->value,  // Thursday
                5 => WeekDay::FRIDAY->value,    // Friday
                6 => WeekDay::SATURDAY->value,  // Saturday
            ];
            $weekDay = $weekDayMapping[$carbonDayOfWeek];
            
            // Get ALL schedules for this day of the week
            $daySchedules = $schedules->where('week_day', $weekDay);
            
            if ($daySchedules->isEmpty()) {
                $this->command->info("No schedules found for {$weekDay} ({$schoolDay->date->format('Y-m-d')})");
                continue;
            }

            $this->command->info("Processing {$daySchedules->count()} schedules for {$weekDay} ({$schoolDay->date->format('Y-m-d')})");

            // Create class session for EVERY schedule on this day
            foreach ($daySchedules as $schedule) {
                // Check if session already exists for this schedule and school day
                $existingSession = ClassSession::where('schedule_id', $schedule->id)
                    ->where('school_day_id', $schoolDay->id)
                    ->first();

                if (!$existingSession) {
                    $teacher = $schedule->teacherSectionSubject->teacher;
                    $subject = $schedule->teacherSectionSubject->subject;
                    $section = $schedule->teacherSectionSubject->section;
                    $classPeriod = $schedule->classPeriod;

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
                    $createdCount++;
                } else {
                    $skippedCount++;
                }
            }
        }
        
        $totalCreated = ClassSession::count();
        $this->command->info("ClassSessionSeeder completed!");
        $this->command->info("Created: {$createdCount} new sessions");
        $this->command->info("Skipped: {$skippedCount} existing sessions"); 
        $this->command->info("Total class sessions in database: {$totalCreated}");
    }

    /**
     * Get random status based on date
     */
    private function getRandomStatus($date): string
    {
        $today = now()->toDateString();
        $dateString = $date->format('Y-m-d');

        if ($dateString < $today) {
            // Past dates - mostly completed
            return rand(1, 10) <= 8 ? ClassSessionStatus::COMPLETED->value : ClassSessionStatus::SCHEDULED->value;
        } elseif ($dateString === $today) {
            // Today - mix of statuses
            $rand = rand(1, 10);
            if ($rand <= 3) return ClassSessionStatus::SCHEDULED->value;
            else return ClassSessionStatus::COMPLETED->value;
        } else {
            // Future dates - mostly scheduled
            return rand(1, 10) <= 9 ? ClassSessionStatus::SCHEDULED->value : ClassSessionStatus::COMPLETED->value;
        }
    }
}