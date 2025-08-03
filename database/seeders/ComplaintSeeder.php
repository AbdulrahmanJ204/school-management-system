<?php

namespace Database\Seeders;

use App\Models\Complaint;
use App\Models\User;
use Illuminate\Database\Seeder;

class ComplaintSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();
        
        if (!$admin) {
            $admin = User::first();
        }

        $users = User::all();

        if ($users->isEmpty()) {
            return;
        }

        // Create some sample complaints
        for ($i = 0; $i < 10; $i++) {
            $user = $users->random();
            
            Complaint::create([
                'user_id' => $user->id,
                'title' => 'شكوى ' . ($i + 1),
                'content' => 'محتوى الشكوى رقم ' . ($i + 1) . ' - هذا نص تجريبي للشكوى.',
                'answer' => $i % 2 == 0 ? 'رد على الشكوى رقم ' . ($i + 1) . ' - تم معالجة الشكوى بنجاح.' : null,
                'created_by' => $admin->id,
            ]);
        }
    }
} 