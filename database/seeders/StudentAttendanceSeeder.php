<?php

namespace Database\Seeders;

use App\Models\ClassSession;
use App\Models\Student;
use App\Models\StudentAttendance;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class StudentAttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = Student::all();
        $classSessions = ClassSession::all();
        $statuses = ['Excused absence', 'Unexcused absence', 'Late'];

        // Create sample attendance records for the last 30 days
        for ($i = 0; $i < 100; $i++) {
            $student = $students->random();
            $classSession = $classSessions->random();
            $status = $statuses[array_rand($statuses)];

            // Check if attendance record already exists for this student and class session
            $existingAttendance = StudentAttendance::where('student_id', $student->id)
                ->where('class_session_id', $classSession->id)
                ->first();

            if (!$existingAttendance) {
                StudentAttendance::create([
                    'student_id' => $student->id,
                    'class_session_id' => $classSession->id,
                    'status' => $status,
                    'created_by' => 1, // Assuming admin user ID is 1
                ]);
            }
        }

        // Create some specific attendance patterns
        $this->createSpecificAttendancePatterns($students, $classSessions);
    }

    private function createSpecificAttendancePatterns($students, $classSessions)
    {
        // Create attendance for specific students with different patterns
        $specificStudents = $students->take(5); // Take first 5 students
        $recentSessions = $classSessions->where('date', '>=', Carbon::now()->subDays(7));

        foreach ($specificStudents as $index => $student) {
            foreach ($recentSessions as $session) {
                // Create different attendance patterns based on student index
                $status = match ($index) {
                    0 => 'Late', // First student is often late
                    1 => 'Excused absence', // Second student has excused absences
                    2 => 'Unexcused absence', // Third student has unexcused absences
                    default => $this->getRandomStatus(),
                };

                // Check if attendance record already exists
                $existingAttendance = StudentAttendance::where('student_id', $student->id)
                    ->where('class_session_id', $session->id)
                    ->first();

                if (!$existingAttendance) {
                    StudentAttendance::create([
                        'student_id' => $student->id,
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
