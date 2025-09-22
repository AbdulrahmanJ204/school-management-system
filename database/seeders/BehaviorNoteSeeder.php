<?php

namespace Database\Seeders;

use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use App\Models\SchoolDay;
use App\Models\User;

class BehaviorNoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @throws Exception
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
            // Pick 4 different random school days for the student
            $randomSchoolDays = $schoolDays->random(min(4, $schoolDays->count()));

            // Select 2 positive notes
            $chosenPositive = array_rand($positiveNotes, 2);
            foreach ((array) $chosenPositive as $index => $key) {
                $behaviorNotes[] = [
                    'student_id'    => $student->id,
                    'school_day_id' => $randomSchoolDays[$index]->id,
                    'behavior_type' => 'positive',
                    'note'          => $positiveNotes[$key],
                    'created_by'    => $createdUsers[array_rand($createdUsers)],
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ];
            }

            // Select 2 negative notes
            $chosenNegative = array_rand($negativeNotes, 2);
            foreach ((array) $chosenNegative as $index => $key) {
                $behaviorNotes[] = [
                    'student_id'    => $student->id,
                    'school_day_id' => $randomSchoolDays[$index + 2]->id,
                    'behavior_type' => 'negative',
                    'note'          => $negativeNotes[$key],
                    'created_by'    => $createdUsers[array_rand($createdUsers)],
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ];
            }
        }

        try {
            $chunks = array_chunk($behaviorNotes, 100);
            foreach ($chunks as $chunk) {
                DB::table('behavior_notes')->insert($chunk);
            }
            $this->command->info('Behavior notes seeded successfully!');
        } catch (Exception $e) {
            $this->command->error('Error inserting behavior notes: ' . $e->getMessage());
            throw $e;
        }
    }
}
