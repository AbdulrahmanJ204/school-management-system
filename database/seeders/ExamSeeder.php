<?php

namespace Database\Seeders;

use App\Enums\ExamType;
use App\Models\Exam;
use App\Models\Grade;
use App\Models\SchoolDay;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;

class ExamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();
        
        if (!$admin) {
            $admin = User::first();
        }

        $schoolDays = SchoolDay::all();
        $grades = Grade::all();
        $subjects = Subject::all();

        if ($schoolDays->isEmpty() || $grades->isEmpty() || $subjects->isEmpty()) {
            return;
        }

        // Create some sample exams
        for ($i = 0; $i < 10; $i++) {
            $subject = $subjects->random();
            $type = ExamType::getValues()[array_rand(ExamType::getValues())];
            Exam::create([
                'school_day_id' => $schoolDays->random()->id,
                'grade_id' => $subject->getGrade()->id,
                'subject_id' => $subject->id,
                'type' => $type,
                'created_by' => $admin->id,
            ]);
        }
    }
} 