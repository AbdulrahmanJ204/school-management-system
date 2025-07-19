<?php

namespace Database\Seeders;

use App\Models\Section;
use App\Models\Semester;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\Year;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StudentEnrollmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = Student::all();
        $currentSemester = Semester::where('year_id', Year::where('is_active', true)->first()->id)->first();

        if (!$currentSemester) {
            return;
        }

        foreach ($students as $student) {
            // Assuming students have a grade_level attribute or similar
            // You may need to adjust this based on your Student model
            $gradeId = rand(1, 13); // Random grade for demo purposes
            $sectionsInGrade = Section::where('grade_id', $gradeId)->get();

            if ($sectionsInGrade->count() > 0) {
                $randomSection = $sectionsInGrade->random();

                StudentEnrollment::create([
                    'student_id' => $student->id,
                    'section_id' => $randomSection->id,
                    'grade_id' => $gradeId,
                    'semester_id' => $currentSemester->id,
                    'created_by' => 1,
                ]);
            }
        }
    }
}
