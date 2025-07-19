<?php

namespace Database\Seeders;

use App\Models\Grade;
use App\Models\Section;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sections = ['A', 'B', 'C', 'D'];
        $grades = Grade::all();

        foreach ($grades as $grade) {
            // Create 2-4 sections per grade depending on grade level
            $sectionCount = $grade->id <= 3 ? 2 : ($grade->id <= 8 ? 3 : 4);

            for ($i = 0; $i < $sectionCount; $i++) {
                Section::create([
                    'title' => $sections[$i],
                    'grade_id' => $grade->id,
                    'created_by' => 1,
                ]);
            }
        }
    }
}
