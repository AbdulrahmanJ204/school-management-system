<?php

namespace Database\Seeders;

use App\Models\Grade;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $grades = [
            ['title' => 'Kindergarten', 'created_by' => 1],
            ['title' => 'Grade 1', 'created_by' => 1],
            ['title' => 'Grade 2', 'created_by' => 1],
            ['title' => 'Grade 3', 'created_by' => 1],
            ['title' => 'Grade 4', 'created_by' => 1],
            ['title' => 'Grade 5', 'created_by' => 1],
            ['title' => 'Grade 6', 'created_by' => 1],
            ['title' => 'Grade 7', 'created_by' => 1],
            ['title' => 'Grade 8', 'created_by' => 1],
            ['title' => 'Grade 9', 'created_by' => 1],
            ['title' => 'Grade 10', 'created_by' => 1],
            ['title' => 'Grade 11', 'created_by' => 1],
            ['title' => 'Grade 12', 'created_by' => 1],
        ];

        foreach ($grades as $grade) {
            Grade::create($grade);
        }
    }
}
