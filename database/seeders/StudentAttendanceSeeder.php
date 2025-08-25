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
        $classSessions = ClassSession::whereIn('status', ['completed', 'scheduled'])->get();
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
                    // For completed sessions, create attendance records
                    if ($classSession->status === 'completed') {
                        $status = $this->getRandomAttendanceStatus();

                        StudentAttendance::create([
                            'student_id' => $student->id,
                            'class_session_id' => $classSession->id,
                            'status' => $status,
                            'created_by' => 1,
                        ]);
                    }
                    // For scheduled sessions, create some attendance records (simulating early attendance tracking)
                    elseif ($classSession->status === 'scheduled' && rand(1, 100) <= 99) {
                        $status = $this->getRandomScheduledAttendanceStatus();

                        StudentAttendance::create([
                            'student_id' => $student->id,
                            'class_session_id' => $classSession->id,
                            'status' => $status,
                            'created_by' => 1,
                        ]);
                    }
                }
            }

            // Create additional attendance records for some students (simulating multiple attendance checks per session)
            if ($classSession->status === 'completed' && rand(1, 100) <= 99) {
                $this->createAdditionalAttendanceRecords($classSession, $enrolledStudents);
            }
        }

        // Generate additional attendance records for the first 5 students to reach 100+ per student
        $this->generateExtraAttendanceForFirstFiveStudents();
    }

    /**
     * Generate extra attendance records for the first 5 students
     */
    private function generateExtraAttendanceForFirstFiveStudents(): void
    {
        $firstFiveStudents = Student::take(5)->get();
        $allClassSessions = ClassSession::all();

        foreach ($firstFiveStudents as $student) {
            // Get current attendance count for this student
            $currentAttendanceCount = StudentAttendance::where('student_id', $student->id)->count();

            // Calculate how many more records we need to reach at least 100
            $targetCount = 100 + rand(10, 50); // 100-150 records per student
            $neededRecords = max(0, $targetCount - $currentAttendanceCount);

            if ($neededRecords > 0) {
                // Get random class sessions that don't have attendance for this student
                $availableSessions = $allClassSessions->filter(function($session) use ($student) {
                    return !StudentAttendance::where('student_id', $student->id)
                        ->where('class_session_id', $session->id)
                        ->exists();
                });

                // If we don't have enough unique sessions, create multiple records for existing sessions
                if ($availableSessions->count() < $neededRecords) {
                    $this->createMultipleAttendanceForStudent($student, $neededRecords);
                } else {
                    // Create attendance for random available sessions
                    $randomSessions = $availableSessions->random(min($neededRecords, $availableSessions->count()));

                    foreach ($randomSessions as $session) {
                        $status = $this->getRandomAttendanceStatus();

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
    }

    /**
     * Create multiple attendance records for a student (for sessions that already have attendance)
     */
    private function createMultipleAttendanceForStudent($student, $count): void
    {
        $classSessions = ClassSession::all();

        for ($i = 0; $i < $count; $i++) {
            $randomSession = $classSessions->random();
            $status = $this->getRandomAttendanceStatus();

            StudentAttendance::create([
                'student_id' => $student->id,
                'class_session_id' => $randomSession->id,
                'status' => $status,
                'created_by' => 1,
            ]);
        }
    }

    /**
     * Create additional attendance records for the same session (simulating multiple attendance checks)
     */
    private function createAdditionalAttendanceRecords($classSession, $enrolledStudents)
    {
        // Select random students for additional attendance records
        $randomStudents = $enrolledStudents->random(min(5, $enrolledStudents->count()));

        foreach ($randomStudents as $enrollment) {
            $student = $enrollment->student;

            // Check if we already have multiple attendance records for this student and session
            $existingCount = StudentAttendance::where('student_id', $student->id)
                ->where('class_session_id', $classSession->id)
                ->count();

            if ($existingCount < 2) { // Allow up to 2 attendance records per student per session
                $status = $this->getRandomAttendanceStatus();

                StudentAttendance::create([
                    'student_id' => $student->id,
                    'class_session_id' => $classSession->id,
                    'status' => $status,
                    'created_by' => 1,
                ]);
            }
        }
    }

    /**
     * Get random attendance status with realistic distribution for completed sessions
     */
    private function getRandomAttendanceStatus(): string
    {
        $rand = rand(1, 100);

        if ($rand <= 70) {
            return 'present'; // 70% present
        } elseif ($rand <= 80) {
            return 'Unexcused absence'; // 10% unexcused absence
        } elseif ($rand <= 85) {
            return 'Late'; // 5% late
        } elseif ($rand <= 90) {
            return 'Excused absence'; // 5% excused absence
        } else {
            return 'Unexcused absence'; // 10% more absences
        }
    }

    /**
     * Get random attendance status for scheduled sessions (mostly present or late)
     */
    private function getRandomScheduledAttendanceStatus(): string
    {
        $rand = rand(1, 100);

        if ($rand <= 50) {
            return 'present'; // 50% present for scheduled sessions
        } elseif ($rand <= 70) {
            return 'Excused absence'; // 20% Excused absence
        } elseif ($rand <= 85) {
            return 'Late'; // 15% late
        } else {
            return 'Unexcused absence'; // 15% absence
        }
    }
}

