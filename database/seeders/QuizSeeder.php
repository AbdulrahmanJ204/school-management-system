<?php

namespace Database\Seeders;

use App\Models\Quiz;
use App\Models\QuizTarget;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teacherId  = 1; // replace with an existing teacher's user_id
        $gradeId    = 1; // replace with an existing grade
        $subjectId  = 1; // replace with an existing subject
        $sectionId  = 1; // replace with an existing section (can be null if targeting all)
        $semesterId = 1; // replace with an existing semester

        // Create a quiz
        $quiz = Quiz::create([
            'name'        => 'Math Midterm',
            'full_score'  => 100,
            'is_active'   => false,
            'created_by'  => $teacherId,
        ]);

        // Attach quiz target
        QuizTarget::create([
            'quiz_id'     => $quiz->id,
            'grade_id'    => $gradeId,
            'subject_id'  => $subjectId,
            'section_id'  => $sectionId,
            'semester_id' => $semesterId,
        ]);
    }
}
