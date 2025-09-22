<?php

namespace Database\Seeders;

use App\Models\ClassPeriod;
use App\Models\SchoolShift;
use App\Enums\ClassPeriodType;
use Illuminate\Database\Seeder;

class ClassPeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schoolShifts = SchoolShift::all();

        if ($schoolShifts->isEmpty()) {
            // Create a default school shift if none exists
            $schoolShift = SchoolShift::create([
                'name' => 'الفترة الصباحية',
                'start_time' => '07:00:00',
                'end_time' => '14:00:00',
                'is_active' => true,
                'created_by' => 1,
            ]);
        } else {
            $schoolShift = $schoolShifts->first();
        }

        // Create class periods for the school shift
        $periods = [
            [
                'name' => 'الحصة الأولى',
                'start_time' => '07:00:00',
                'end_time' => '07:45:00',
                'period_order' => 1,
                'type' => ClassPeriodType::STUDY,
                'duration_minutes' => 45,
            ],
            [
                'name' => 'الحصة الثانية',
                'start_time' => '07:50:00',
                'end_time' => '08:35:00',
                'period_order' => 2,
                'type' => ClassPeriodType::STUDY,
                'duration_minutes' => 45,
            ],
//            [
//                'name' => 'الحصة الثالثة',
//                'start_time' => '08:40:00',
//                'end_time' => '09:25:00',
//                'period_order' => 3,
//                'type' => ClassPeriodType::STUDY,
//                'duration_minutes' => 45,
//            ],
            [
                'name' => 'فسحة',
                'start_time' => '09:25:00',
                'end_time' => '09:45:00',
                'period_order' => 4,
                'type' => ClassPeriodType::BREAK,
                'duration_minutes' => 20,
            ],
//            [
//                'name' => 'الحصة الرابعة',
//                'start_time' => '09:45:00',
//                'end_time' => '10:30:00',
//                'period_order' => 5,
//                'type' => ClassPeriodType::STUDY,
//                'duration_minutes' => 45,
//            ],
//            [
//                'name' => 'الحصة الخامسة',
//                'start_time' => '10:35:00',
//                'end_time' => '11:20:00',
//                'period_order' => 6,
//                'type' => ClassPeriodType::STUDY,
//                'duration_minutes' => 45,
//            ],
//            [
//                'name' => 'الحصة السادسة',
//                'start_time' => '11:25:00',
//                'end_time' => '12:10:00',
//                'period_order' => 7,
//                'type' => ClassPeriodType::STUDY,
//                'duration_minutes' => 45,
//            ],
//            [
//                'name' => 'فسحة الغداء',
//                'start_time' => '12:10:00',
//                'end_time' => '12:40:00',
//                'period_order' => 8,
//                'type' => ClassPeriodType::BREAK,
//                'duration_minutes' => 30,
//            ],
//            [
//                'name' => 'الحصة السابعة',
//                'start_time' => '12:40:00',
//                'end_time' => '13:25:00',
//                'period_order' => 9,
//                'type' => ClassPeriodType::STUDY,
//                'duration_minutes' => 45,
//            ],
//            [
//                'name' => 'الحصة الثامنة',
//                'start_time' => '13:30:00',
//                'end_time' => '14:00:00',
//                'period_order' => 10,
//                'type' => ClassPeriodType::STUDY,
//                'duration_minutes' => 30,
//            ],
        ];

        foreach ($periods as $period) {
            ClassPeriod::create([
                'name' => $period['name'],
                'start_time' => $period['start_time'],
                'end_time' => $period['end_time'],
                'school_shift_id' => $schoolShift->id,
                'period_order' => $period['period_order'],
                'type' => $period['type'],
                'duration_minutes' => $period['duration_minutes'],
                'created_by' => 1,
            ]);
        }
    }
}
