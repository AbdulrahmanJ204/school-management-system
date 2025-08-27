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
                'year_id' => 2, // 2024-2025 (active year)
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'الصف الثاني الابتدائي',
                'year_id' => 2, // 2024-2025 (active year)
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // [
            //     'title' => 'الصف الثالث الابتدائي',
            //     'year_id' => 2, // 2024-2025 (active year)
            //     'created_by' => 1,
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'title' => 'الصف الرابع الابتدائي',
            //     'year_id' => 2, // 2024-2025 (active year)
            //     'created_by' => 1,
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'title' => 'الصف الخامس الابتدائي',
            //     'year_id' => 2, // 2024-2025 (active year)
            //     'created_by' => 1,
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'title' => 'الصف السادس الابتدائي',
            //     'year_id' => 2, // 2024-2025 (active year)
            //     'created_by' => 1,
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],

            // // Middle School
            // [
            //     'title' => 'الصف السابع الإعدادي',
            //     'year_id' => 2, // 2024-2025 (active year)
            //     'created_by' => 1,
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'title' => 'الصف الثامن الإعدادي',
            //     'year_id' => 2, // 2024-2025 (active year)
            //     'created_by' => 1,
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'title' => 'الصف التاسع الإعدادي',
            //     'year_id' => 2, // 2024-2025 (active year)
            //     'created_by' => 1,
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],

            // // High School
            // [
            //     'title' => 'الصف العاشر الثانوي',
            //     'year_id' => 2, // 2024-2025 (active year)
            //     'created_by' => 1,
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'title' => 'الصف الحادي عشر الثانوي',
            //     'year_id' => 2, // 2024-2025 (active year)
            //     'created_by' => 1,
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'title' => 'الصف البكلوريا الثانوي',
            //     'year_id' => 2, // 2024-2025 (active year)
            //     'created_by' => 1,
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
        ];

        DB::table('grades')->insert($grades);
    }
}
