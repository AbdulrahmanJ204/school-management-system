<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\SchoolDay;
use App\Models\Semester;
use DateTime;



class SchoolDaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schoolDays = [];

        // Generate school days for current semester (semester_id = 3, 2024-2025 first semester)
        $startDate = Carbon::parse('2024-09-01');
        $endDate = Carbon::parse('2025-01-31');

        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            // Skip Fridays and Saturdays (weekend in many Arab countries)
            if (!in_array($currentDate->dayOfWeek, [Carbon::FRIDAY, Carbon::SATURDAY])) {
                // Most days are study days
                $type = 'study';

                // Add some exam days (last week of December and January)
                if (($currentDate->month == 12 && $currentDate->day >= 25) ||
                    ($currentDate->month == 1 && $currentDate->day >= 20)) {
                    $type = 'exam';
                }

                $schoolDays[] = [
                    'date' => $currentDate->format('Y-m-d'),
                    'semester_id' => 3, // Current active semester
                    'type' => $type,
                    'created_by' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            $currentDate->addDay();
        }

        // Insert in chunks to avoid memory issues
        $chunks = array_chunk($schoolDays, 100);
        foreach ($chunks as $chunk) {
            DB::table('school_days')->insert($chunk);
        }
    }
}
