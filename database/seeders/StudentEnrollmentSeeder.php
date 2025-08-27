<?php

namespace Database\Seeders;

use App\Models\Grade;
use App\Models\Section;
use App\Models\Semester;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\Year;
use Illuminate\Database\Seeder;

class StudentEnrollmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = Student::all();
        $currentYear = Year::where('is_active', true)->first();
        $currentSemester = Semester::where('year_id', $currentYear->id)->first();

        if (!$currentSemester) {
            return;
        }

        $grades = Grade::with('sections')->get(); // load all grades with sections
        $sections = $grades->flatMap->sections;   // flatten into single collection of sections

        $totalStudents = $students->count();      // e.g. 100
        $totalSections = $sections->count();      // e.g. 9
        $basePerSection = intdiv($totalStudents, $totalSections); // e.g. 11
        $extra = $totalStudents % $totalSections;                 // remainder (some sections get 1 extra)

        $studentIndex = 0;

        foreach ($sections as $index => $section) {
            // number of students for this section
            $studentsForThisSection = $basePerSection + ($index < $extra ? 1 : 0);

            for ($i = 0; $i < $studentsForThisSection && $studentIndex < $totalStudents; $i++) {
                $student = $students[$studentIndex];

                StudentEnrollment::create([
                    'student_id' => $student->id,
                    'section_id' => $section->id,
                    'grade_id' => $section->grade_id,
                    'semester_id' => $currentSemester->id,
                    'year_id' => $currentSemester->year_id,
                    'created_by' => 1,
                ]);

                $studentIndex++;
            }
        }
    }
}
