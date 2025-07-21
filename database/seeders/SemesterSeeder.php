<?php

namespace Database\Seeders;

use App\Models\Semester;
use App\Models\Year;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SemesterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $years = Year::all();

        foreach ($years as $year) {
            // First Semester
            Semester::create([
                'year_id' => $year->id,
                'name' => 'First Semester',
                'start_date' => $year->start_date,
                'end_date' => date('Y-m-d', strtotime($year->start_date . ' +4 months')),
                'created_by' => 1,
            ]);

            // Second Semester
            Semester::create([
                'year_id' => $year->id,
                'name' => 'Second Semester',
                'start_date' => date('Y-m-d', strtotime($year->start_date . ' +5 months')),
                'end_date' => $year->end_date,
                'created_by' => 1,
            ]);
        }
    }
}
