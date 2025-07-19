<?php

namespace Database\Seeders;

use App\Models\SchoolDay;
use App\Models\Semester;
use DateTime;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SchoolDaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $semesters = Semester::all();

        foreach ($semesters as $semester) {
            $this->createSchoolDaysForSemester($semester);
        }
    }
    private function createSchoolDaysForSemester($semester)
    {
        $startDate = new DateTime($semester->start_date);
        $endDate = new DateTime($semester->end_date);
        $currentDate = clone $startDate;

        // Calculate exam period (last 2 weeks of semester)
        $examStartDate = clone $endDate;
        $examStartDate->modify('-14 days');

        while ($currentDate <= $endDate) {
            $dayOfWeek = $currentDate->format('N'); // 1 (Monday) to 7 (Sunday)

            // Skip weekends (Saturday = 6, Friday = 5)
            if ($dayOfWeek < 5 || $dayOfWeek > 6) {
                // Determine if it's exam period or study period
                $type = $currentDate >= $examStartDate ? 'exam' : 'study';

                // Skip some random days to simulate holidays/breaks
                if (rand(1, 10) > 8) {
                    $currentDate->modify('+1 day');
                    continue;
                }

                SchoolDay::create([
                    'date' => $currentDate->format('Y-m-d'),
                    'semester_id' => $semester->id,
                    'type' => $type,
                    'created_by' => 1,
                ]);
            }

            $currentDate->modify('+1 day');
        }
    }
}
