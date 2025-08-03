<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Grade;
use App\Models\Year;

class GradeYearSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $grades = Grade::all();
        $years = Year::all();

        if ($grades->isEmpty() || $years->isEmpty()) {
            $this->command->warn('Grades or Years not found. Please run GradeSeeder and YearSeeder first.');
            return;
        }

        $settings = [];

        foreach ($years as $year) {
            foreach ($grades as $grade) {
                $settings[] = [
                    'year_id' => $year->id,
                    'grade_id' => $grade->id,
                    'max_failed_subjects' => rand(2, 4), // Random between 2-4 failed subjects
                    'help_marks' => rand(5, 15), // Random help marks between 5-15
                    'created_by' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('grade_year_settings')->insert($settings);
    }
} 