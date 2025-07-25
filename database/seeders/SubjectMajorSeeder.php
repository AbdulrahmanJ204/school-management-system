<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Grade;
use App\Models\User;

class SubjectMajorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first grade and user IDs (adjust as needed)
        $gradeId = Grade::first()->id ?? 1;
        $userId = User::first()->id ?? 1;

        $subjectMajors = [
            [
                'grade_id' => $gradeId,
                'name' => 'Science',
                'code' => 'SCI',
                'success_rate' => 85,
                'created_by' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'grade_id' => $gradeId,
                'name' => 'Mathematics',
                'code' => 'MATH',
                'success_rate' => 80,
                'created_by' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'grade_id' => $gradeId,
                'name' => 'Literature',
                'code' => 'LIT',
                'success_rate' => 75,
                'created_by' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'grade_id' => $gradeId,
                'name' => 'Languages',
                'code' => 'LANG',
                'success_rate' => 82,
                'created_by' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'grade_id' => $gradeId,
                'name' => 'Social Studies',
                'code' => 'SOC',
                'success_rate' => 78,
                'created_by' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'grade_id' => $gradeId,
                'name' => 'Arts',
                'code' => 'ART',
                'success_rate' => 90,
                'created_by' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('subject_majors')->insert($subjectMajors);
    }
}
