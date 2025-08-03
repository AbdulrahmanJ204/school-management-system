<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mainSubjects = [
            // Primary School Main Subjects
            [
                'grade_id' => 7,
                'name' => 'الرياضيات',
                'code' => 'MATH-7',
                'success_rate' => 40,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'grade_id' => 7,
                'name' => 'اللغة العربية',
                'code' => 'AR-7',
                'success_rate' => 50,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'grade_id' => 7,
                'name' => 'العلوم',
                'code' => 'SCI-7',
                'success_rate' => 50,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('main_subjects')->insert($mainSubjects);

        $subjects = [
            [
                'name' => 'التحليل الرياضي',
                'main_subject_id' => 1,
                'code' => 'ANALYSIS-7',
                'full_mark' => 100,
                'homework_percentage' => 15,
                'oral_percentage' => 5,
                'activity_percentage' => 15,
                'quiz_percentage' => 25,
                'exam_percentage' => 40,
                'num_class_period' => 4,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'الجبر',
                'main_subject_id' => 1,
                'code' => 'ALGEBRA-7',
                'full_mark' => 100,
                'homework_percentage' => 20,
                'oral_percentage' => 10,
                'activity_percentage' => 10,
                'quiz_percentage' => 20,
                'exam_percentage' => 40,
                'num_class_period' => 4,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'اللغة العربية',
                'main_subject_id' => 2,
                'code' => 'AR-7',
                'full_mark' => 100,
                'homework_percentage' => 10,
                'oral_percentage' => 10,
                'activity_percentage' => 10,
                'quiz_percentage' => 20,
                'exam_percentage' => 50,
                'num_class_period' => 6,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'علم الأحياء',
                'main_subject_id' => 3,
                'code' => 'BIO-7',
                'full_mark' => 100,
                'homework_percentage' => 10,
                'oral_percentage' => 15,
                'activity_percentage' => 15,
                'quiz_percentage' => 20,
                'exam_percentage' => 40,
                'num_class_period' => 4,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'الفيزياء',
                'main_subject_id' => 3,
                'code' => 'PHY-7',
                'full_mark' => 100,
                'homework_percentage' => 10,
                'oral_percentage' => 10,
                'activity_percentage' => 20,
                'quiz_percentage' => 20,
                'exam_percentage' => 40,
                'num_class_period' => 4,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'الكيمياء',
                'main_subject_id' => 3,
                'code' => 'CHEM-7',
                'full_mark' => 100,
                'homework_percentage' => 10,
                'oral_percentage' => 10,
                'activity_percentage' => 20,
                'quiz_percentage' => 20,
                'exam_percentage' => 40,
                'num_class_period' => 4,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('subjects')->insert($subjects);
    }
}
