<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\StudentMark;
use App\Models\StudentAttendance;
use App\Models\ClassSession;
use App\Models\Subject;
use App\Models\Section;
use App\Models\Grade;
use App\Models\Semester;
use App\Models\Year;
use Illuminate\Support\Facades\DB;

class StudentProfileDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding student profile data...');

        // Ensure we have active year and semester
        $activeYear = Year::where('is_active', true)->first();
        if (!$activeYear) {
            $this->command->warn('No active year found. Please run YearSeeder first.');
            return;
        }

        $activeSemester = Semester::where('year_id', $activeYear->id)->first();
        if (!$activeSemester) {
            $this->command->warn('No semester found for active year. Please run SemesterSeeder first.');
            return;
        }

        // Get students with enrollments
        $enrollments = StudentEnrollment::where('semester_id', $activeSemester->id)
            ->with(['student.user', 'section.grade', 'semester'])
            ->get();

        if ($enrollments->isEmpty()) {
            $this->command->warn('No student enrollments found. Please run StudentEnrollmentSeeder first.');
            return;
        }

        $this->command->info("Found {$enrollments->count()} student enrollments to process.");

        // Generate comprehensive marks for each student
        $this->generateStudentMarks($enrollments);

        // Generate attendance data
        $this->generateAttendanceData($enrollments);

        $this->command->info('Student profile data seeding completed!');
    }

    /**
     * Generate comprehensive student marks
     */
    private function generateStudentMarks($enrollments): void
    {
        $subjects = Subject::all();
        
        if ($subjects->isEmpty()) {
            $this->command->warn('No subjects found. Please run SubjectSeeder first.');
            return;
        }

        $marks = [];

        foreach ($enrollments as $enrollment) {
            // Get subjects for this enrollment's grade
            $gradeSubjects = $subjects->where('main_subject_id', 
                $enrollment->section->grade->mainSubjects->first()->id ?? 1);

            foreach ($gradeSubjects as $subject) {
                // Check if mark already exists
                $existingMark = StudentMark::where('enrollment_id', $enrollment->id)
                    ->where('subject_id', $subject->id)
                    ->first();

                if (!$existingMark) {
                    // Generate realistic marks (60-95 range for most students)
                    $homework = rand(60, 95);
                    $oral = rand(70, 95);
                    $activity = rand(65, 90);
                    $quiz = rand(60, 95);
                    $exam = rand(55, 95);

                    // Calculate total based on subject percentages (only quiz and exam for GPA)
                    $total = ($quiz * $subject->quiz_percentage / 100) +
                            ($exam * $subject->exam_percentage / 100);

                    $marks[] = [
                        'enrollment_id' => $enrollment->id,
                        'subject_id' => $subject->id,
                        'homework' => $homework,
                        'oral' => $oral,
                        'activity' => $activity,
                        'quiz' => $quiz,
                        'exam' => $exam,
                        'total' => round($total, 2),
                        'created_by' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        if (!empty($marks)) {
            DB::table('student_marks')->insert($marks);
            $this->command->info("Created " . count($marks) . " student marks.");
        }
    }

    /**
     * Generate attendance data
     */
    private function generateAttendanceData($enrollments): void
    {
        // Get completed class sessions for the active semester
        $classSessions = ClassSession::whereHas('schoolDay', function($query) use ($enrollments) {
                $semesterId = $enrollments->first()->semester_id;
                $query->whereHas('semester', function($semesterQuery) use ($semesterId) {
                    $semesterQuery->where('id', $semesterId);
                });
            })
            ->where('status', 'completed')
            ->get();

        if ($classSessions->isEmpty()) {
            $this->command->warn('No completed class sessions found. Please run ClassSessionSeeder first.');
            return;
        }

        $attendanceRecords = [];

        foreach ($classSessions as $classSession) {
            // Get students enrolled in this section
            $sectionEnrollments = $enrollments->where('section_id', $classSession->section_id);

            foreach ($sectionEnrollments as $enrollment) {
                $student = $enrollment->student;
                
                $status = $this->getRandomAttendanceStatus();
                
                // Only create records for absences and late arrivals (using updateOrCreate to respect unique constraint)
                if ($status !== 'present') {
                    StudentAttendance::updateOrCreate(
                        [
                            'student_id' => $student->id,
                            'class_session_id' => $classSession->id,
                        ],
                        [
                            'status' => $status,
                            'created_by' => 1,
                        ]
                    );
                }
            }
        }

        $this->command->info("Attendance records created using updateOrCreate to respect unique constraint.");
    }

    /**
     * Get random attendance status with realistic distribution
     */
    private function getRandomAttendanceStatus(): string
    {
        $rand = rand(1, 100);
        
        if ($rand <= 75) {
            return 'present'; // 75% present (no record created)
        } elseif ($rand <= 85) {
            return 'Unexcused absence'; // 10% unexcused absence
        } elseif ($rand <= 90) {
            return 'Late'; // 5% late
        } elseif ($rand <= 95) {
            return 'Excused absence'; // 5% excused absence
        } else {
            return 'Unexcused absence'; // 5% more absences
        }
    }
}
