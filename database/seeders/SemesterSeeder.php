<?php

namespace Database\Seeders;

use App\Models\Year;
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
                'start_date' => Year::find(1)->start_date,
                'end_date' => Year::find(1)->start_date->addMonths(1)->format('Y-m-d'),
                'created_by' => 1,
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'year_id' => 1,
                'name' => 'الفصل الدراسي الثاني',
                'start_date' => Year::find(1)->start_date->addMonths(1)->addDay()->format('Y-m-d'),
                'end_date' => Year::find(1)->end_date,
                'created_by' => 1,
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 2024-2025 Academic Year (Active)
            [
                'year_id' => 2,
                'name' => 'الفصل الدراسي الأول',
                'start_date' => Year::find(2)->start_date,
                'end_date' => Year::find(2)->start_date->addMonths(1)->format('Y-m-d'),
                'created_by' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'year_id' => 2,
                'name' => 'الفصل الدراسي الثاني',
                'start_date' => Year::find(2)->start_date->addMonths(1)->addDay()->format('Y-m-d'),
                'end_date' => Year::find(2)->end_date,
                'created_by' => 1,
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 2025-2026 Academic Year
            [
                'year_id' => 3,
                'name' => 'الفصل الدراسي الأول',
                'start_date' => Year::find(3)->start_date,
                'end_date' => Year::find(3)->start_date->addMonths(1)->format('Y-m-d'),
                'created_by' => 1,
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'year_id' => 3,
                'name' => 'الفصل الدراسي الثاني',
                'start_date' => Year::find(3)->start_date->addMonths(1)->addDay()->format('Y-m-d'),
                'end_date' => Year::find(3)->end_date,
                'created_by' => 1,
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('semesters')->insert($semesters);
    }
}
