<?php

namespace Database\Seeders;

use App\Models\Semester;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Year;
use Database\Factories\UserFactory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(
            [
                RolePermissionSeeder::class,
                AdminSeeder::class,
                YearSeeder::class,
                SemesterSeeder::class,
                SchoolDaySeeder::class,
                GradeSeeder::class,
                SectionSeeder::class,
                SubjectSeeder::class,
            ]
        );

        User::factory()->student()->count(10)->create();
        User::factory()->teacher()->count(5)->create();
        User::factory()->admin()->count(3)->create();
        $this->call([
            StudentEnrollmentSeeder::class,
            NewsSeeder::class,
            SubjectMajorSeeder::class,
            SubjectSeeder::class,
              TeacherSectionSubjectTempSeeder::class,
        ]);
    }
}
