<?php

namespace Database\Seeders;

use App\Models\TeacherAttendance;
use App\Models\ClassSession;
use App\Models\Teacher;
use Illuminate\Database\Seeder;

class TeacherAttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classSessions = ClassSession::where('status', 'completed')->get();
        $teachers = Teacher::all();

        if ($classSessions->isEmpty() || $teachers->isEmpty()) {
            return;
        }

        foreach ($classSessions as $classSession) {
            // Check if teacher attendance record already exists for this session
            $existingAttendance = TeacherAttendance::where('class_session_id', $classSession->id)
                ->where('teacher_id', $classSession->teacher_id)
                ->first();

            if (!$existingAttendance) {
                TeacherAttendance::create([
                    'class_session_id' => $classSession->id,
                    'teacher_id' => $classSession->teacher_id,
                    'status' => $this->getRandomTeacherAttendanceStatus(),
                    'created_by' => 1,
                ]);
            }
        }
    }

    /**
     * Get random teacher attendance status with realistic distribution
     */
    private function getRandomTeacherAttendanceStatus(): string
    {
        $rand = rand(1, 100);
        
        if ($rand <= 85) {
            return 'Unexcused absence'; // 85% present (teachers have higher attendance rate)
        } elseif ($rand <= 90) {
            return 'Late'; // 5% late
        } else {
            return 'Excused absence'; // 5% excused
        }
    }
}
