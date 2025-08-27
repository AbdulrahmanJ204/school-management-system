<?php

namespace Database\Seeders;

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
        $sectionNames = ['الأولى', 'الثانية', 'الثالثة'];

        // Create sections for each grade (grades 1-2, since only 2 grades exist)
        for ($gradeId = 1; $gradeId <= 2; $gradeId++) {
            // Primary grades (1-2) have 2 sections each
            $numSections = 2;

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
