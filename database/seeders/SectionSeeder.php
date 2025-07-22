<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sections = [];
        $sectionNames = ['الأولى', 'الثانية', 'الثالثة', 'الرابعة', 'الخامسة'];

        // Create sections for each grade (grades 1-12)
        for ($gradeId = 1; $gradeId <= 12; $gradeId++) {
            // Primary grades (1-6) have fewer sections
            $numSections = $gradeId <= 6 ? 3 : 5;

            for ($i = 0; $i < $numSections; $i++) {
                $sections[] = [
                    'title' => $sectionNames[$i],
                    'grade_id' => $gradeId,
                    'created_by' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('sections')->insert($sections);
    }
}
