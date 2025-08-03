<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        $enrollments = StudentEnrollment::with(['section', 'semester'])->get();
        $subjects = Subject::all();

        if ($enrollments->isEmpty() || $subjects->isEmpty()) {
            $this->command->warn('Student Enrollments or Subjects not found. Please run StudentEnrollmentSeeder and SubjectSeeder first.');
            return;
        }

        $marks = [];

        foreach ($enrollments as $enrollment) {
            // Get subjects for this enrollment's section
            $sectionSubjects = $subjects->where('main_subject_id', $enrollment->section->grade->mainSubjects->first()->id ?? 1);
            
            foreach ($sectionSubjects as $subject) {
                // Generate random marks based on subject percentages
                $homework = rand(0, 100);
                $oral = rand(0, 100);
                $activity = rand(0, 100);
                $quiz = rand(0, 100);
                $exam = rand(0, 100);

                // Calculate total based on subject percentages
                $total = ($homework * $subject->homework_percentage / 100) +
                        ($oral * $subject->oral_percentage / 100) +
                        ($activity * $subject->activity_percentage / 100) +
                        ($quiz * $subject->quiz_percentage / 100) +
                        ($exam * $subject->exam_percentage / 100);

                $marks[] = [
                    'enrollment_id' => $enrollment->id,
                    'subject_id' => $subject->id,
                    'homework' => $homework,
                    'oral' => $oral,
                    'activity' => $activity,
                    'quiz' => $quiz,
                    'exam' => $exam,
                    'total' => round($total),
                    'created_by' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('student_marks')->insert($marks);
    }
} 