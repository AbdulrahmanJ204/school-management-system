<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $grades = [
            // Primary School
            [
                'title' => 'الصف الأول الابتدائي',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'الصف الثاني الابتدائي',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'الصف الثالث الابتدائي',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'الصف الرابع الابتدائي',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'الصف الخامس الابتدائي',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'الصف السادس الابتدائي',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Middle School
            [
                'title' => 'الصف السابع الإعدادي',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'الصف الثامن الإعدادي',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'الصف التاسع الإعدادي',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // High School
            [
                'title' => 'الصف العاشر الثانوي',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'الصف الحادي عشر الثانوي',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'الصف البكلوريا الثانوي',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('grades')->insert($grades);
    }
}
