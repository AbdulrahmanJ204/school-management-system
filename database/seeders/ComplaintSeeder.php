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

        // Get all students (users with user_type = 'student')
        $students = User::where('user_type', 'student')->get();

        if ($students->isEmpty()) {
            return;
        }

        // Create 2 complaints for each student
        foreach ($students as $student) {
            // First complaint with answer
            Complaint::create([
                'user_id' => $student->id,
                'title' => 'شكوى ' . $student->first_name . ' - ' . 1,
                'content' => 'محتوى الشكوى الأولى للطالب ' . $student->first_name . ' - هذا نص تجريبي للشكوى.',
                'answer' => 'رد على شكوى الطالب ' . $student->first_name . ' - تم معالجة الشكوى بنجاح.',
                'created_by' => $admin->id,
            ]);

            // Second complaint without answer
            Complaint::create([
                'user_id' => $student->id,
                'title' => 'شكوى ' . $student->first_name . ' - ' . 2,
                'content' => 'محتوى الشكوى الثانية للطالب ' . $student->first_name . ' - هذا نص تجريبي للشكوى.',
                'answer' => null,
                'created_by' => $admin->id,
            ]);
        }
    }
} 