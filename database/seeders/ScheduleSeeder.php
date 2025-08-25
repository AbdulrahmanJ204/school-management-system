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
        $sections = \App\Models\Section::all();

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

        if ($classPeriods->isEmpty() || $teacherSectionSubjects->isEmpty() || $sections->isEmpty()) {
            $this->command->info("Skipping ScheduleSeeder: Missing required data");
            $this->command->info("Class Periods (STUDY): {$classPeriods->count()}");
            $this->command->info("Teacher-Section-Subjects: {$teacherSectionSubjects->count()}");
            $this->command->info("Sections: {$sections->count()}");
            return;
        }

        // Create schedules for each day of the week
        $weekDays = [WeekDay::SUNDAY, WeekDay::MONDAY, WeekDay::TUESDAY, WeekDay::WEDNESDAY, WeekDay::THURSDAY];

        $this->command->info("Creating schedules for {$sections->count()} sections, " . count($weekDays) . " weekdays, and {$classPeriods->count()} class periods each...");
        
        $createdCount = 0;
        $skippedCount = 0;

        // For each section
        foreach ($sections as $section) {
            // For each weekday
            foreach ($weekDays as $weekDay) {
                // For each of the 8 class periods
                foreach ($classPeriods as $classPeriod) {
                    // Check if schedule already exists for this combination
                    $existingSchedule = Schedule::where('timetable_id', $timetable->id)
                        ->where('week_day', $weekDay)
                        ->where('class_period_id', $classPeriod->id)
                        ->whereHas('teacherSectionSubject', function($query) use ($section) {
                            $query->where('section_id', $section->id);
                        })
                        ->first();

                    if (!$existingSchedule) {
                        // Find a teacher-section-subject for this specific section
                        $sectionTeacherSubjects = $teacherSectionSubjects->where('section_id', $section->id);
                        
                        if ($sectionTeacherSubjects->isNotEmpty()) {
                            $teacherSectionSubject = $sectionTeacherSubjects->random();
                            
                            Schedule::create([
                                'timetable_id' => $timetable->id,
                                'class_period_id' => $classPeriod->id,
                                'teacher_section_subject_id' => $teacherSectionSubject->id,
                                'week_day' => $weekDay,
                                'created_by' => 1,
                            ]);
                            
                            $createdCount++;
                        }
                    } else {
                        $skippedCount++;
                    }
                }
            }
        }
        
        $this->command->info("Schedule generation completed:");
        $this->command->info("Created: {$createdCount} schedules");
        $this->command->info("Skipped (already exists): {$skippedCount} schedules");
        $this->command->info("Expected total: " . ($sections->count() * count($weekDays) * $classPeriods->count()));
    }
}
