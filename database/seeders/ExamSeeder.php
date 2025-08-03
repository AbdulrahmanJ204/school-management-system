<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\Grade;
use App\Models\SchoolDay;
use App\Models\MainSubject;
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
        $mainSubjects = MainSubject::all();

        if ($schoolDays->isEmpty() || $grades->isEmpty() || $mainSubjects->isEmpty()) {
            return;
        }

        // Create some sample exams
        for ($i = 0; $i < 10; $i++) {
            Exam::create([
                'school_day_id' => $schoolDays->random()->id,
                'grade_id' => $grades->random()->id,
                'main_subject_id' => $mainSubjects->random()->id,
                'created_by' => $admin->id,
            ]);
        }
    }
} 