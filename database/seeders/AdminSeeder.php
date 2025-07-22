<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdminUser = User::create([
            'first_name' => 'admin',
            'father_name' => 'super',
            'last_name' => 'ruler',
            'gender' => 'male',
            'birth_date' => '2003-01-01',
            'email' => 'admin@gmail.com',
            'phone' => '0935946431',
            'password' => '1234567890',
            'image' => 'user_images/default.png',
            'user_type' => 'admin',
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Hash::make('password');

        $superAdminRole = Role::where('name', 'super_admin')->first();
        $superAdminUser->assignRole($superAdminRole);

        Admin::create([
            'user_id' => $superAdminUser->id,
            'created_by' => 1, // This could be the admin's own ID or different logic
        ]);
    }
}
