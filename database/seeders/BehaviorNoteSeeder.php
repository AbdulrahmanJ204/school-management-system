<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use App\Models\SchoolDay;
use App\Models\User;

class BehaviorNoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = Student::all();
        $schoolDays = SchoolDay::all();
        $users = User::all();

        if ($students->isEmpty()) {
            $this->command->warn('No students found. Please run StudentSeeder first.');
            return;
        }

        if ($schoolDays->isEmpty()) {
            $this->command->warn('No school days found. Please run SchoolDaySeeder first.');
            return;
        }

        if ($users->isEmpty()) {
            $this->command->warn('No users found. Please run UserSeeder first.');
            return;
        }

        // ملاحظات إيجابية
        $positiveNotes = [
            'مشاركة نشطة في الأنشطة الصفية',
            'تعاون ممتاز مع المعلم',
            'مساعدة زملائه في الدرس',
            'أداء ممتاز في الأنشطة الجماعية',
            'مبادرة في حل المشاكل',
            'احترام المعلم والزملاء',
            'تنظيم ممتاز للمواد الدراسية',
            'مشاركة إيجابية في النقاش',
            'إظهار روح القيادة',
            'التزام بالقوانين الصفية',
            'حل الواجبات في الوقت المحدد',
            'إبداع في تقديم الأفكار',
            'تفوق في الأنشطة الأكاديمية',
            'سلوك مثالي داخل الصف',
            'مساعدة المعلم في تنظيم الصف',
            'تشجيع زملائه على المشاركة',
            'أداء متميز في العروض التقديمية',
            'احترام قوانين المدرسة',
            'روح المبادرة في الأنشطة',
            'تفاعل إيجابي مع الدروس'
        ];

        // ملاحظات سلبية
        $negativeNotes = [
            'عدم إحضار الواجب المدرسي',
            'التأخر المتكرر عن الحصة',
            'إزعاج زملائه أثناء الدرس',
            'عدم التركيز في الدرس',
            'التحدث بدون إذن من المعلم',
            'عدم احترام المعلم',
            'عدم المشاركة في الأنشطة',
            'إهمال في أداء المهام المطلوبة',
            'سلوك غير مناسب في الصف',
            'عدم اتباع التعليمات',
            'استخدام الهاتف أثناء الدرس',
            'عدم إحضار الكتب والأدوات',
            'الغياب بدون عذر مقبول',
            'عدم احترام زملائه',
            'إحداث فوضى في الصف',
            'عدم أداء الواجبات',
            'السلوك العدواني',
            'عدم الاستماع للمعلم',
            'تشتيت انتباه الآخرين',
            'عدم المحافظة على نظافة الصف'
        ];

        $behaviorNotes = [];
        $createdUsers = $users->pluck('id')->toArray();

        foreach ($students as $student) {
            $noteCount = rand(2, 5);

            $randomSchoolDays = $schoolDays->random(min($noteCount, $schoolDays->count()));

            for ($i = 0; $i < $noteCount && $i < $randomSchoolDays->count(); $i++) {
                $behaviorType = rand(1, 10) <= 7 ? 'positive' : 'negative';

                if ($behaviorType === 'positive') {
                    $note = $positiveNotes[array_rand($positiveNotes)];
                } else {
                    $note = $negativeNotes[array_rand($negativeNotes)];
                }

                $behaviorNotes[] = [
                    'student_id' => $student->id,
                    'school_day_id' => $randomSchoolDays[$i]->id,
                    'behavior_type' => $behaviorType,
                    'note' => $note,
                    'created_by' => $createdUsers[array_rand($createdUsers)],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        try {
            $chunks = array_chunk($behaviorNotes, 100);

            foreach ($chunks as $chunk) {
                DB::table('behavior_notes')->insert($chunk);
            }

        } catch (\Exception $e) {
            $this->command->error('Error inserting behavior notes: ' . $e->getMessage());
            throw $e;
        }
    }
}
