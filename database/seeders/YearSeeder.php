<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class YearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $years = [
            [
                'name' => '2023-2024',
                'start_date' => now()->subYears(1)->format('Y-m-d'),
                'end_date' => now()->subYears(1)->addMonths(8)->format('Y-m-d'),
                'is_active' => false,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '2024-2025',
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addMonths(8)->format('Y-m-d'),
                'is_active' => true,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '2025-2026',
                'start_date' => now()->addYears(1)->format('Y-m-d'),
                'end_date' => now()->addYears(1)->addMonths(8)->format('Y-m-d'),
                'is_active' => false,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('years')->insert($years);
    }
}
