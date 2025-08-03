<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TeacherSectionSubjectTempSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teachers = DB::table('teachers')->pluck('id');
        $grades = DB::table('grades')->pluck('id');
        $subjects = DB::table('subjects')->pluck('id');
        $sections = DB::table('sections')->pluck('id');
        $users = DB::table('users')->pluck('id');

        // Seed 10 rows for example
        for ($i = 0; $i < 10; $i++) {
            DB::table('teacher_section_subjects')->insert([
                'teacher_id' => $teachers->random(),
                'grade_id' => $grades->random(),
                'subject_id' => $subjects->random(),
                'section_id' => $sections->random(),
                'is_active' => rand(0, 1),
                'num_class_period' => rand(1, 6),
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => $users->random(),
            ]);
        }
    }
}
