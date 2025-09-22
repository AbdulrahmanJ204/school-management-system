<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\StudentEnrollment;
use App\Models\Subject;

class StudentMarkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $enrollments = StudentEnrollment::with(['section.grade.mainSubjects'])->get();

        if ($enrollments->isEmpty()) {
            $this->command->warn('Student Enrollments not found. Please run StudentEnrollmentSeeder first.');
            return;
        }

        $marks = [];

        foreach ($enrollments as $enrollment) {
            $grade = $enrollment->section->grade;

            if (!$grade || $grade->mainSubjects->isEmpty()) {
                $this->command->warn("No subjects found for Grade ID {$grade->id} (Enrollment ID {$enrollment->id}). Skipping.");
                continue;
            }

            // collect all subjects belonging to this grade's main subjects
            $gradeSubjectIds = $grade->mainSubjects->pluck('id')->toArray();

            $sectionSubjects = Subject::whereIn('main_subject_id', $gradeSubjectIds)->get();

            if ($sectionSubjects->isEmpty()) {
                $this->command->warn("No subjects linked to Grade ID {$grade->id}. Skipping.");
                continue;
            }

            foreach ($sectionSubjects as $subject) {
                // Generate random marks
                $homework = rand(0, 100);
                $oral = rand(0, 100);
                $activity = rand(0, 100);
                $quiz = rand(0, 100);
                $exam = rand(0, 100);

                // Calculate weighted total
                $total = ($homework * $subject->homework_percentage / 100) +
                    ($oral * $subject->oral_percentage / 100) +
                    ($activity * $subject->activity_percentage / 100) +
                    ($quiz * $subject->quiz_percentage / 100) +
                    ($exam * $subject->exam_percentage / 100);

                $marks[] = [
                    'enrollment_id' => $enrollment->id,
                    'subject_id'    => $subject->id,
                    'homework'      => $homework,
                    'oral'          => $oral,
                    'activity'      => $activity,
                    'quiz'          => $quiz,
                    'exam'          => $exam,
                    'total'         => round($total),
                    'created_by'    => 1,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ];
            }
        }

        if (!empty($marks)) {
            DB::table('student_marks')->insert($marks);
            $this->command->info(count($marks) . ' student marks inserted successfully.');
        } else {
            $this->command->warn('No student marks were generated.');
        }
    }
}
