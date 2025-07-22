<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\SubjectMajor;
use App\Models\User;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get subject major IDs (adjust as needed)
        $scienceMajorId = SubjectMajor::where('code', 'SCI')->first()->id ?? 1;
        $mathMajorId = SubjectMajor::where('code', 'MATH')->first()->id ?? 2;
        $litMajorId = SubjectMajor::where('code', 'LIT')->first()->id ?? 3;
        $langMajorId = SubjectMajor::where('code', 'LANG')->first()->id ?? 4;
        $socMajorId = SubjectMajor::where('code', 'SOC')->first()->id ?? 5;
        $artMajorId = SubjectMajor::where('code', 'ART')->first()->id ?? 6;

        $userId = User::first()->id ?? 1;

        $subjects = [
            // Science subjects
            [
                'name' => 'Biology',
                'subject_major_id' => $scienceMajorId,
                'code' => 'BIO101',
                'full_mark' => 100,
                'homework_percentage' => 15,
                'oral_percentage' => 10,
                'activity_percentage' => 10,
                'quiz_percentage' => 15,
                'exam_percentage' => 50,
                'num_class_period' => 4,
                'created_by' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Chemistry',
                'subject_major_id' => $scienceMajorId,
                'code' => 'CHEM101',
                'full_mark' => 100,
                'homework_percentage' => 15,
                'oral_percentage' => 10,
                'activity_percentage' => 15,
                'quiz_percentage' => 10,
                'exam_percentage' => 50,
                'num_class_period' => 4,
                'created_by' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Physics',
                'subject_major_id' => $scienceMajorId,
                'code' => 'PHYS101',
                'full_mark' => 100,
                'homework_percentage' => 15,
                'oral_percentage' => 5,
                'activity_percentage' => 15,
                'quiz_percentage' => 15,
                'exam_percentage' => 50,
                'num_class_period' => 5,
                'created_by' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Mathematics subjects
            [
                'name' => 'Algebra',
                'subject_major_id' => $mathMajorId,
                'code' => 'ALG101',
                'full_mark' => 100,
                'homework_percentage' => 20,
                'oral_percentage' => 5,
                'activity_percentage' => 10,
                'quiz_percentage' => 15,
                'exam_percentage' => 50,
                'num_class_period' => 5,
                'created_by' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Geometry',
                'subject_major_id' => $mathMajorId,
                'code' => 'GEO101',
                'full_mark' => 100,
                'homework_percentage' => 20,
                'oral_percentage' => 5,
                'activity_percentage' => 15,
                'quiz_percentage' => 10,
                'exam_percentage' => 50,
                'num_class_period' => 4,
                'created_by' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Literature subjects
            [
                'name' => 'English Literature',
                'subject_major_id' => $litMajorId,
                'code' => 'ENG101',
                'full_mark' => 100,
                'homework_percentage' => 15,
                'oral_percentage' => 20,
                'activity_percentage' => 15,
                'quiz_percentage' => 10,
                'exam_percentage' => 40,
                'num_class_period' => 4,
                'created_by' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Arabic Literature',
                'subject_major_id' => $litMajorId,
                'code' => 'AR101',
                'full_mark' => 100,
                'homework_percentage' => 15,
                'oral_percentage' => 25,
                'activity_percentage' => 10,
                'quiz_percentage' => 10,
                'exam_percentage' => 40,
                'num_class_period' => 5,
                'created_by' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Languages subjects
            [
                'name' => 'French',
                'subject_major_id' => $langMajorId,
                'code' => 'FR101',
                'full_mark' => 100,
                'homework_percentage' => 15,
                'oral_percentage' => 25,
                'activity_percentage' => 15,
                'quiz_percentage' => 10,
                'exam_percentage' => 35,
                'num_class_period' => 3,
                'created_by' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'German',
                'subject_major_id' => $langMajorId,
                'code' => 'GR101',
                'full_mark' => 100,
                'homework_percentage' => 15,
                'oral_percentage' => 25,
                'activity_percentage' => 15,
                'quiz_percentage' => 10,
                'exam_percentage' => 35,
                'num_class_period' => 3,
                'created_by' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Social Studies subjects
            [
                'name' => 'History',
                'subject_major_id' => $socMajorId,
                'code' => 'HIST101',
                'full_mark' => 100,
                'homework_percentage' => 15,
                'oral_percentage' => 15,
                'activity_percentage' => 20,
                'quiz_percentage' => 10,
                'exam_percentage' => 40,
                'num_class_period' => 3,
                'created_by' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Geography',
                'subject_major_id' => $socMajorId,
                'code' => 'GEO101',
                'full_mark' => 100,
                'homework_percentage' => 15,
                'oral_percentage' => 10,
                'activity_percentage' => 20,
                'quiz_percentage' => 15,
                'exam_percentage' => 40,
                'num_class_period' => 3,
                'created_by' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Arts subjects
            [
                'name' => 'Visual Arts',
                'subject_major_id' => $artMajorId,
                'code' => 'ART101',
                'full_mark' => 100,
                'homework_percentage' => 10,
                'oral_percentage' => 10,
                'activity_percentage' => 40,
                'quiz_percentage' => 5,
                'exam_percentage' => 35,
                'num_class_period' => 2,
                'created_by' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Music',
                'subject_major_id' => $artMajorId,
                'code' => 'MUS101',
                'full_mark' => 100,
                'homework_percentage' => 10,
                'oral_percentage' => 20,
                'activity_percentage' => 35,
                'quiz_percentage' => 5,
                'exam_percentage' => 30,
                'num_class_period' => 2,
                'created_by' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('subjects')->insert($subjects);
    }
}
