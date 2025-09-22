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
        // Get more class sessions - include both completed and scheduled
        $classSessions = ClassSession::whereIn('status', ['completed', 'scheduled'])->get();
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
                // For completed sessions, create attendance records
                if ($classSession->status === 'completed') {
                    TeacherAttendance::create([
                        'class_session_id' => $classSession->id,
                        'teacher_id' => $classSession->teacher_id,
                        'status' => $this->getRandomTeacherAttendanceStatus(),
                        'created_by' => 1,
                    ]);
                }
                // For scheduled sessions, create some attendance records (simulating early attendance tracking)
                elseif ($classSession->status === 'scheduled' && rand(1, 100) <= 99) {
                    TeacherAttendance::create([
                        'class_session_id' => $classSession->id,
                        'teacher_id' => $classSession->teacher_id,
                        'status' => $this->getRandomScheduledTeacherAttendanceStatus(),
                        'created_by' => 1,
                    ]);
                }
            }

            // Create additional attendance records for some sessions (simulating multiple attendance checks)
            if ($classSession->status === 'completed' && rand(1, 100) <= 99) {
                $this->createAdditionalTeacherAttendanceRecords($classSession);
            }
        }
    }

    /**
     * Create additional teacher attendance records for the same session
     */
    private function createAdditionalTeacherAttendanceRecords($classSession): void
    {
        // Check if we already have multiple attendance records for this teacher and session
        $existingCount = TeacherAttendance::where('teacher_id', $classSession->teacher_id)
            ->where('class_session_id', $classSession->id)
            ->count();

        if ($existingCount < 2) { // Allow up to 2 attendance records per teacher per session
            TeacherAttendance::create([
                'class_session_id' => $classSession->id,
                'teacher_id' => $classSession->teacher_id,
                'status' => $this->getRandomTeacherAttendanceStatus(),
                'created_by' => 1,
            ]);
        }
    }

    /**
     * Get random teacher attendance status with realistic distribution for completed sessions
     */
    private function getRandomTeacherAttendanceStatus(): string
    {
        $rand = rand(1, 100);

        if ($rand <= 70) {
            return 'lateness'; // 70% lateness (teachers are mostly on time)
        } elseif ($rand <= 85) {
            return 'justified_absent'; // 15% excused absent
        } else {
            return 'absent'; // 15% unexcused absent
        }
    }

    /**
     * Get random teacher attendance status for scheduled sessions
     */
    private function getRandomScheduledTeacherAttendanceStatus(): string
    {
        $rand = rand(1, 100);

        if ($rand <= 50) {
            return 'present'; // 50% present for scheduled sessions
        } elseif ($rand <= 70) {
            return 'justified_absent'; // 20% justified_absent
        } elseif ($rand <= 85) {
            return 'lateness'; // 15% lateness
        } else {
            return 'absent'; // 15% absent
        }
    }
}
