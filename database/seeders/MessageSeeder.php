<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class MessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('Users not found. Please run UserSeeder first.');
            return;
        }

        $messages = [];

        $titles = [
            'تذكير بالواجب المنزلي',
            'معلومات مهمة للطلاب',
            'تحديث الجدول الدراسي',
            'إشعار بموعد الاختبار',
            'تذكير بالأنشطة المدرسية',
            'معلومات عن الرحلات المدرسية',
            'تحديث قواعد الفصل',
            'إشعار بالاجتماع المدرسي',
            'تذكير بالمواد المطلوبة',
            'معلومات عن المسابقات المدرسية',
            'تحديث سياسة الحضور',
            'إشعار بموعد التسجيل',
            'تذكير بالواجبات المدرسية',
            'معلومات عن المكتبة المدرسية',
            'تحديث قواعد السلوك'
        ];

        $messageContents = [
            'يرجى إحضار الواجب المنزلي غداً في الحصة الأولى',
            'سيتم عقد اجتماع مهم مع أولياء الأمور الأسبوع القادم',
            'تم تحديث الجدول الدراسي، يرجى مراجعة التغييرات',
            'سيتم إجراء اختبار في مادة الرياضيات يوم الخميس',
            'سيتم تنظيم رحلة مدرسية إلى المتحف العلمي',
            'يرجى الالتزام بقواعد الفصل وعدم إزعاج الزملاء',
            'سيتم عقد اجتماع مع المعلمين لمناقشة التحسينات',
            'يرجى إحضار الكتب والمواد المطلوبة للدرس',
            'سيتم تنظيم مسابقة في القراءة والكتابة',
            'يرجى الالتزام بمواعيد الحضور والانصراف',
            'سيتم فتح باب التسجيل للأنشطة الإضافية',
            'يرجى إكمال الواجبات المدرسية في الوقت المحدد',
            'المكتبة المدرسية متاحة للطلاب خلال الفسح',
            'يرجى الالتزام بقواعد السلوك المدرسية',
            'سيتم تنظيم ورشة عمل في العلوم التطبيقية'
        ];

        foreach ($users as $user) {
            // Create 1-4 messages per user
            $numMessages = rand(1, 4);
            
            for ($i = 0; $i < $numMessages; $i++) {
                $titleIndex = array_rand($titles);
                $contentIndex = array_rand($messageContents);
                
                $messages[] = [
                    'user_id' => $user->id,
                    'title' => $titles[$titleIndex],
                    'message' => $messageContents[$contentIndex],
                    'created_by' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('messages')->insert($messages);
    }
} 