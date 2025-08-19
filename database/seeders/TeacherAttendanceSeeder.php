<?php

namespace Database\Seeders;

use App\Models\ClassSession;
use App\Models\Teacher;
use App\Models\TeacherAttendance;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TeacherAttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teachers = Teacher::all();
        $classSessions = ClassSession::all();
        $statuses = ['Excused absence', 'Unexcused absence', 'Late'];

        // Create sample attendance records for the last 30 days
        for ($i = 0; $i < 50; $i++) {
            $teacher = $teachers->random();
            $classSession = $classSessions->random();
            $status = $statuses[array_rand($statuses)];

            // Check if attendance record already exists for this teacher and class session
            $existingAttendance = TeacherAttendance::where('teacher_id', $teacher->id)
                ->where('class_session_id', $classSession->id)
                ->first();

            if (!$existingAttendance) {
                TeacherAttendance::create([
                    'teacher_id' => $teacher->id,
                    'class_session_id' => $classSession->id,
                    'status' => $status,
                    'created_by' => 1, // Assuming admin user ID is 1
                ]);
            }
        }

        // Create some specific attendance patterns
        $this->createSpecificAttendancePatterns($teachers, $classSessions);
    }

    private function createSpecificAttendancePatterns($teachers, $classSessions)
    {
        // Create attendance for specific teachers with different patterns
        $specificTeachers = $teachers->take(3); // Take first 3 teachers
        $recentSessions = $classSessions->where('date', '>=', Carbon::now()->subDays(7));

        foreach ($specificTeachers as $index => $teacher) {
            foreach ($recentSessions as $session) {
                // Create different attendance patterns based on teacher index
                $status = match ($index) {
                    0 => 'Late', // First teacher is sometimes late
                    1 => 'Excused absence', // Second teacher has occasional excused absences
                    2 => 'Unexcused absence', // Third teacher has rare unexcused absences
                    default => $this->getRandomStatus(),
                };

                // Check if attendance record already exists
                $existingAttendance = TeacherAttendance::where('teacher_id', $teacher->id)
                    ->where('class_session_id', $session->id)
                    ->first();

                if (!$existingAttendance) {
                    TeacherAttendance::create([
                        'teacher_id' => $teacher->id,
                        'class_session_id' => $session->id,
                        'status' => $status,
                        'created_by' => 1,
                    ]);
                }
            }
        }
    }

    private function getRandomStatus(): string
    {
        $statuses = ['Excused absence', 'Unexcused absence', 'Late'];
        return $statuses[array_rand($statuses)];
    }
}
