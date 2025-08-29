<?php

namespace Database\Seeders;

use App\Models\Year;
use App\Models\Semester;
use App\Models\StudentEnrollment;
use App\Models\StudentMark;
use App\Models\StudentAttendance;
use App\Models\ClassSession;
use Illuminate\Database\Seeder;

class StudentProfileDataSeeder extends Seeder
{
    public function run(): void
    {
        $year = Year::where('is_active', true)->first();
        $semester = Semester::where('is_active', true)->first();

        if (!$year || !$semester) {
            $this->command->warn('No active year or semester found.');
            return;
        }

        $enrollments = StudentEnrollment::with(['student', 'section.grade.mainSubjects'])
            ->where('semester_id', $semester->id)
            ->get();

        foreach ($enrollments as $index => $enrollment) {
            // -------------------------------
            // 1. Student Marks (all subjects)
            // -------------------------------
            foreach ($enrollment->section->grade->mainSubjects as $subject) {
                StudentMark::updateOrCreate(
                    [
                        'enrollment_id' => $enrollment->id,
                        'subject_id'    => $subject->id,
                    ],
                    [
                        // deterministic mark based on index → no randomness
                        'score' => 60 + (($index * 7 + $subject->id) % 36), // 60–95
                        'max_score' => 100,
                    ]
                );
            }

            // -------------------------------
            // 2. Student Attendance (all sessions)
            // -------------------------------
            $classSessions = ClassSession::where('section_id', $enrollment->section_id)
                ->where('semester_id', $semester->id)
                ->where('status', 'completed')
                ->get();

            foreach ($classSessions as $sessionIndex => $session) {
                // Deterministic attendance pattern:
                // Rotate statuses: present, unexcused, late, excused
                $statuses = ['present', 'absent', 'late', 'justified_absent'];
                $status = $statuses[($index + $sessionIndex) % count($statuses)];

                StudentAttendance::updateOrCreate(
                    [
                        'student_id'     => $enrollment->student_id,
                        'class_session_id' => $session->id,
                    ],
                    [
                        'status' => $status,
                    ]
                );
            }
        }

        $this->command->info('Student profile data seeded successfully (marks + attendance).');
    }
}
