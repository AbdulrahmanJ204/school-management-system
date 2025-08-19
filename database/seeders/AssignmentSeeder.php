<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\ClassSession;
use App\Models\Section;
use App\Models\Subject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sections = Section::all();
        $subjects = Subject::all();
        $classSessions = ClassSession::all();
        $types = ['homework', 'oral', 'quiz', 'project'];

        // Create sample assignments
        for ($i = 0; $i < 50; $i++) {
            $section = $sections->random();
            $subject = $subjects->random();
            $assignedSession = $classSessions->random();
            $dueSession = $classSessions->where('date', '>', $assignedSession->date)->random();
            $type = $types[array_rand($types)];

            Assignment::create([
                'assigned_session_id' => $assignedSession->id,
                'due_session_id' => $dueSession ? $dueSession->id : null,
                'type' => $type,
                'title' => $this->generateAssignmentTitle($type, $subject->name),
                'description' => $this->generateAssignmentDescription($type, $subject->name),
                'photo' => null, // You can add sample photos later if needed
                'subject_id' => $subject->id,
                'section_id' => $section->id,
                'created_by' => 1, // Assuming admin user ID is 1
            ]);
        }

        // Create some specific assignments for demonstration
        $this->createSpecificAssignments($sections, $subjects, $classSessions);
    }

    private function createSpecificAssignments($sections, $subjects, $classSessions)
    {
        // Create homework assignments
        $homeworkSubjects = $subjects->take(3);
        foreach ($homeworkSubjects as $subject) {
            $section = $sections->random();
            $assignedSession = $classSessions->random();
            $dueSession = $classSessions->where('date', '>', $assignedSession->date)->random();

            Assignment::create([
                'assigned_session_id' => $assignedSession->id,
                'due_session_id' => $dueSession ? $dueSession->id : null,
                'type' => 'homework',
                'title' => "واجب منزلي - {$subject->name}",
                'description' => "حل التمارين من الصفحة 45 إلى 50 في كتاب {$subject->name}. يجب تسليم الواجب في الجلسة القادمة.",
                'photo' => null,
                'subject_id' => $subject->id,
                'section_id' => $section->id,
                'created_by' => 1,
            ]);
        }

        // Create quiz assignments
        $quizSubjects = $subjects->take(2);
        foreach ($quizSubjects as $subject) {
            $section = $sections->random();
            $assignedSession = $classSessions->random();

            Assignment::create([
                'assigned_session_id' => $assignedSession->id,
                'due_session_id' => null, // Quizzes are usually done in the same session
                'type' => 'quiz',
                'title' => "اختبار قصير - {$subject->name}",
                'description' => "اختبار قصير في {$subject->name} يشمل الفصل الثالث. مدة الاختبار 20 دقيقة.",
                'photo' => null,
                'subject_id' => $subject->id,
                'section_id' => $section->id,
                'created_by' => 1,
            ]);
        }

        // Create project assignments
        $projectSubjects = $subjects->take(2);
        foreach ($projectSubjects as $subject) {
            $section = $sections->random();
            $assignedSession = $classSessions->random();
            $dueSession = $classSessions->where('date', '>', $assignedSession->date)->take(3)->last();

            Assignment::create([
                'assigned_session_id' => $assignedSession->id,
                'due_session_id' => $dueSession ? $dueSession->id : null,
                'type' => 'project',
                'title' => "مشروع - {$subject->name}",
                'description' => "إعداد مشروع بحثي في {$subject->name}. يجب أن يتضمن المشروع 10 صفحات على الأقل مع المراجع.",
                'photo' => null,
                'subject_id' => $subject->id,
                'section_id' => $section->id,
                'created_by' => 1,
            ]);
        }

        // Create oral assignments
        $oralSubjects = $subjects->take(2);
        foreach ($oralSubjects as $subject) {
            $section = $sections->random();
            $assignedSession = $classSessions->random();

            Assignment::create([
                'assigned_session_id' => $assignedSession->id,
                'due_session_id' => null, // Oral presentations are usually done in the same session
                'type' => 'oral',
                'title' => "عرض شفهي - {$subject->name}",
                'description' => "إعداد عرض شفهي لمدة 10 دقائق حول موضوع من {$subject->name}. يجب إحضار الشرائح والمواد المساعدة.",
                'photo' => null,
                'subject_id' => $subject->id,
                'section_id' => $section->id,
                'created_by' => 1,
            ]);
        }
    }

    private function generateAssignmentTitle($type, $subjectName): string
    {
        $titles = [
            'homework' => [
                "واجب منزلي - {$subjectName}",
                "تمارين {$subjectName}",
                "واجب {$subjectName} الأسبوعي",
            ],
            'oral' => [
                "عرض شفهي - {$subjectName}",
                "تقرير شفهي - {$subjectName}",
                "عرض {$subjectName}",
            ],
            'quiz' => [
                "اختبار قصير - {$subjectName}",
                "امتحان {$subjectName}",
                "اختبار {$subjectName}",
            ],
            'project' => [
                "مشروع {$subjectName}",
                "بحث {$subjectName}",
                "مشروع نهائي - {$subjectName}",
            ],
        ];

        $typeTitles = $titles[$type] ?? ["واجب {$subjectName}"];
        return $typeTitles[array_rand($typeTitles)];
    }

    private function generateAssignmentDescription($type, $subjectName): string
    {
        $descriptions = [
            'homework' => [
                "حل التمارين من الصفحة المحددة في كتاب {$subjectName}. يجب تسليم الواجب في الجلسة القادمة.",
                "إكمال التمارين المطلوبة في {$subjectName} والتحضير للجلسة القادمة.",
                "واجب منزلي في {$subjectName} يشمل الفصل الحالي.",
            ],
            'oral' => [
                "إعداد عرض شفهي لمدة 10 دقائق حول موضوع من {$subjectName}. يجب إحضار الشرائح والمواد المساعدة.",
                "تقرير شفهي في {$subjectName} مع استخدام الوسائل البصرية.",
                "عرض تقديمي في {$subjectName} لمدة 15 دقيقة.",
            ],
            'quiz' => [
                "اختبار قصير في {$subjectName} يشمل الفصل الحالي. مدة الاختبار 20 دقيقة.",
                "امتحان {$subjectName} يشمل المواضيع المطروحة في الفصل الأخير.",
                "اختبار {$subjectName} مع أسئلة متعددة الخيارات.",
            ],
            'project' => [
                "إعداد مشروع بحثي في {$subjectName}. يجب أن يتضمن المشروع 10 صفحات على الأقل مع المراجع.",
                "مشروع {$subjectName} يشمل البحث والتجارب العملية.",
                "مشروع نهائي في {$subjectName} مع عرض تقديمي.",
            ],
        ];

        $typeDescriptions = $descriptions[$type] ?? ["واجب في {$subjectName}"];
        return $typeDescriptions[array_rand($typeDescriptions)];
    }
}
