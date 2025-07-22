<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SemesterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $semesters = [
            // 2023-2024 Academic Year
            [
                'year_id' => 1,
                'name' => 'الفصل الدراسي الأول',
                'start_date' => '2023-09-01',
                'end_date' => '2024-01-31',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'year_id' => 1,
                'name' => 'الفصل الدراسي الثاني',
                'start_date' => '2024-02-01',
                'end_date' => '2024-06-30',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 2024-2025 Academic Year (Active)
            [
                'year_id' => 2,
                'name' => 'الفصل الدراسي الأول',
                'start_date' => '2024-09-01',
                'end_date' => '2025-01-31',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'year_id' => 2,
                'name' => 'الفصل الدراسي الثاني',
                'start_date' => '2025-02-01',
                'end_date' => '2025-06-30',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 2025-2026 Academic Year
            [
                'year_id' => 3,
                'name' => 'الفصل الدراسي الأول',
                'start_date' => '2025-09-01',
                'end_date' => '2026-01-31',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'year_id' => 3,
                'name' => 'الفصل الدراسي الثاني',
                'start_date' => '2026-02-01',
                'end_date' => '2026-06-30',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('semesters')->insert($semesters);
    }
}
