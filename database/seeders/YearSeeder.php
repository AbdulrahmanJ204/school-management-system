<?php

namespace Database\Seeders;

use App\Models\Year;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class YearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $years = [
            [
                'name' => '2023-2024 Academic Year',
                'start_date' => '2023-09-01',
                'end_date' => '2024-06-30',
                'is_active' => false,
                'created_by' => 1,
            ],
            [
                'name' => '2024-2025 Academic Year',
                'start_date' => '2024-09-01',
                'end_date' => '2025-06-30',
                'is_active' => true,
                'created_by' => 1,
            ],
            [
                'name' => '2025-2026 Academic Year',
                'start_date' => '2025-09-01',
                'end_date' => '2026-06-30',
                'is_active' => false,
                'created_by' => 1,
            ],
        ];

        foreach ($years as $year) {
            Year::create($year);
        }

    }
}
