<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Teacher;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Grade;

class TeacherSectionSubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teachers = Teacher::all();
        $sections = Section::all();
        $subjects = Subject::all();
        $grades = Grade::all();

        if ($teachers->isEmpty() || $sections->isEmpty() || $subjects->isEmpty() || $grades->isEmpty()) {
            $this->command->warn('Teachers, Sections, Subjects, or Grades not found. Please run the respective seeders first.');
            return;
        }

        $teacherSectionSubjects = [];

        foreach ($sections as $section) {
            // Get subjects for this section's grade
            $gradeSubjects = $subjects->where('main_subject_id', $section->grade->mainSubjects->first()->id ?? 1);

            foreach ($gradeSubjects as $subject) {
                // Randomly assign teachers to sections and subjects
                if (rand(1, 3) === 1) { // 33% chance to create assignment
                    $teacher = $teachers->random();

                    $teacherSectionSubjects[] = [
                        'teacher_id' => $teacher->id,
                        'grade_id' => $section->grade_id,
                        'subject_id' => $subject->id,
                        'section_id' => $section->id,
                        'is_active' => 1,
                        'num_class_period' => rand(2, 4), // Random number of class periods
                        'created_by' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        DB::table('teacher_section_subjects')->insert($teacherSectionSubjects);
    }
}
