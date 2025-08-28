<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Teacher;
use App\Models\Section;
use App\Models\Subject;

class TeacherSectionSubjectSeeder extends Seeder
{
    public function run(): void
    {
        $teachers = Teacher::all();
        $sections = Section::all();
        $subjects = Subject::all();

        if ($teachers->isEmpty() || $sections->isEmpty() || $subjects->isEmpty()) {
            $this->command->warn('Teachers, Sections, or Subjects not found. Please run the respective seeders first.');
            return;
        }

        $teacherSectionSubjects = [];
        $teacherIndex = 0; // counter for stable rotation

        foreach ($sections as $section) {
            foreach ($subjects as $subject) {
                // Always assign deterministically using round robin
                $teacher = $teachers[$teacherIndex % $teachers->count()];

                $teacherSectionSubjects[] = [
                    'teacher_id'        => $teacher->id,
                    'grade_id'          => $section->grade_id,
                    'subject_id'        => $subject->id,
                    'section_id'        => $section->id,
                    'is_active'         => 1,
                    // stable period rule: e.g. subject id modulo 3 + 2 => values 2,3,4
                    'num_class_period'  => ($subject->id % 3) + 2,
                    'created_by'        => 1,
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ];

                $teacherIndex++; // next teacher in sequence
            }
        }

        DB::table('teacher_section_subjects')->insert($teacherSectionSubjects);
    }
}
