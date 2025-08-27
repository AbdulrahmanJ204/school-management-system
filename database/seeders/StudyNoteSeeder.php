<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use App\Models\SchoolDay;
use App\Models\Subject;
use App\Enums\NoteTypeEnum;

class StudyNoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = Student::all();
        $schoolDays = SchoolDay::all();
        $subjects = Subject::all();

        if ($students->isEmpty() || $schoolDays->isEmpty() || $subjects->isEmpty()) {
            $this->command->warn('Students, School Days, or Subjects not found. Please run the respective seeders first.');
            return;
        }

        $studyNotes = [];

        $notes = [
            'أداء ممتاز في الدرس',
            'تحسن ملحوظ في الفهم',
            'يحتاج إلى مزيد من التركيز',
            'مشاركة نشطة في الدرس',
            'أداء جيد في الواجبات',
            'يحتاج إلى مراجعة إضافية',
            'فهم جيد للمفاهيم',
            'تحسن في حل المسائل',
            'يحتاج إلى تحسين في الكتابة',
            'أداء متوسط في الاختبار',
            'مشاركة محدودة في النقاش',
            'يحتاج إلى تطوير مهارات القراءة',
            'أداء جيد في الأنشطة',
            'يحتاج إلى تحسين في الحساب',
            'فهم ممتاز للمفاهيم الجديدة'
        ];

        foreach ($students as $student) {
            // Ensure each student has one note from each NoteTypeEnum
            foreach (NoteTypeEnum::cases() as $noteType) {
                $schoolDay = $schoolDays->random();
                $subject = $subjects->random();

                $studyNotes[] = [
                    'student_id'    => $student->id,
                    'school_day_id' => $schoolDay->id,
                    'subject_id'    => $subject->id,
                    'note_type'     => $noteType->value,
                    'note'          => $notes[array_rand($notes)],
                    'marks'         => rand(1, 5), // Random marks between 1-5
                    'created_by'    => 1,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ];
            }
        }

        // Insert in chunks for performance
        $chunks = array_chunk($studyNotes, 100);
        foreach ($chunks as $chunk) {
            DB::table('study_notes')->insert($chunk);
        }

        $this->command->info('Study notes seeded successfully, each student got one note per type!');
    }
}
