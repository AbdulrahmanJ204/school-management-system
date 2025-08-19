<?php

namespace Database\Seeders;

use App\Models\StudentAttendance;
use App\Models\ClassSession;
use App\Models\Student;
use App\Models\StudentEnrollment;
use Illuminate\Database\Seeder;

class StudentAttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classSessions = ClassSession::where('status', 'completed')->get();
        $students = Student::all();

        if ($classSessions->isEmpty() || $students->isEmpty()) {
            return;
        }

        foreach ($classSessions as $classSession) {
            // Get students enrolled in this section for the current year/semester
            $enrolledStudents = StudentEnrollment::where('section_id', $classSession->section_id)
                ->whereHas('semester', function($query) use ($classSession) {
                    $query->whereHas('year', function($yearQuery) {
                        $yearQuery->where('is_active', true);
                    });
                })
                ->with('student')
                ->get();

            if ($enrolledStudents->isEmpty()) {
                continue;
            }

            // Create attendance records for each enrolled student
            foreach ($enrolledStudents as $enrollment) {
                $student = $enrollment->student;
                
                // Check if attendance record already exists for this student and session
                $existingAttendance = StudentAttendance::where('student_id', $student->id)
                    ->where('class_session_id', $classSession->id)
                    ->first();

                if (!$existingAttendance) {
                    StudentAttendance::create([
                        'student_id' => $student->id,
                        'class_session_id' => $classSession->id,
                        'status' => $this->getRandomAttendanceStatus(),
                        'created_by' => 1,
                    ]);
                }
            }
        }
    }

    /**
     * Get random attendance status with realistic distribution
     */
    private function getRandomAttendanceStatus(): string
    {
        $rand = rand(1, 100);
        
        if ($rand <= 75) {
            return 'present'; // 75% present
        } elseif ($rand <= 85) {
            return 'absent'; // 10% absent
        } elseif ($rand <= 90) {
            return 'late'; // 5% late
        } elseif ($rand <= 95) {
            return 'excused'; // 5% excused
        } else {
            return 'sick'; // 5% sick
        }
    }
}
