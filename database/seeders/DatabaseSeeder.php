<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Factories\AdminFactory;
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
            ]
        );

        User::factory()->student()->count(10)->create();
        User::factory()->teacher()->count(5)->create();
        User::factory()->admin()->count(3)->create();
    }
}
