<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\ClassSession;
use App\Models\Subject;
use App\Models\Section;
use Illuminate\Database\Seeder;

class AssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classSessions = ClassSession::where('status', 'completed')->get();
        $subjects = Subject::all();
        $sections = Section::all();

        if ($classSessions->isEmpty() || $subjects->isEmpty() || $sections->isEmpty()) {
            return;
        }

        // Assignment types available
        $assignmentTypes = ['homework', 'oral', 'quiz'];

        // Sample assignment data for different subjects
        $assignmentData = [
            'homework' => [
                'الرياضيات' => [
                    'حل مسائل المعادلات التربيعية من 1 إلى 10',
                    'إكمال ورقة العمل الهندسية',
                    'تدريب على التعبيرات الجبرية',
                    'مراجعة تمارين الفصل الخامس',
                    'حل المسائل اللفظية من الكتاب المدرسي'
                ],
                'العلوم' => [
                    'قراءة الفصل الثالث والإجابة على الأسئلة',
                    'إكمال تقرير المختبر',
                    'البحث في عملية التمثيل الضوئي',
                    'كتابة ملخص للتجربة',
                    'التحضير لجلسة المختبر القادمة'
                ],
                'اللغة العربية' => [
                    'كتابة مقال من 500 كلمة عن كتابك المفضل',
                    'إكمال تمارين القواعد',
                    'قراءة الفصل المخصص من الرواية',
                    'تدريب على الكلمات المفردة',
                    'كتابة قصة إبداعية'
                ],
                'التاريخ' => [
                    'البحث في الحضارات القديمة',
                    'كتابة تقرير عن الأحداث التاريخية',
                    'إكمال مهمة الجدول الزمني',
                    'الدراسة للاختبار القادم',
                    'قراءة الوثائق المصدرية'
                ]
            ],
            'oral' => [
                'اللغة العربية' => [
                    'تقديم تقرير كتابك',
                    'تدريب على تمارين النطق',
                    'إلقاء خطاب عن الأحداث الحالية',
                    'المشاركة في المناقشة الجماعية',
                    'تقديم نتائج بحثك'
                ],
                'العلوم' => [
                    'تقديم نتائج تجربتك',
                    'شرح المفاهيم العلمية',
                    'مناقشة القضايا البيئية',
                    'تقديم مشروع بحثك',
                    'شرح إجراءات المختبر'
                ],
                'التاريخ' => [
                    'تقديم سيرة شخصية تاريخية',
                    'مناقشة الأحداث التاريخية',
                    'تقديم نتائج بحثك',
                    'شرح الأهمية التاريخية',
                    'المشاركة في المناظرة'
                ]
            ],
            'quiz' => [
                'الرياضيات' => [
                    'اختبار الجبر - الفصل الثالث',
                    'اختبار الهندسة - المثلثات',
                    'اختبار التفاضل والتكامل - المشتقات',
                    'اختبار الإحصاء - الاحتمالية',
                    'اختبار علم المثلثات - دائرة الوحدة'
                ],
                'العلوم' => [
                    'اختبار الأحياء - بنية الخلية',
                    'اختبار الكيمياء - التفاعلات الكيميائية',
                    'اختبار الفيزياء - القوى والحركة',
                    'اختبار علوم الأرض - الصفائح التكتونية',
                    'اختبار علوم البيئة - النظم البيئية'
                ],
                'اللغة العربية' => [
                    'اختبار القواعد - أجزاء الكلام',
                    'اختبار المفردات - الوحدة الخامسة',
                    'اختبار الأدب - شيله',
                    'اختبار الكتابة - بنية المقال',
                    'اختبار القراءة - الفهم'
                ]
            ]
        ];

        // Create assignments for each class session
        foreach ($classSessions as $classSession) {
            // Randomly decide if this session should have an assignment (30% chance)
            if (rand(1, 100) <= 30) {
                $type = $assignmentTypes[array_rand($assignmentTypes)];
                $subject = $classSession->subject;
                $subjectName = $subject->name ?? 'General';

                // Get assignment titles for this subject and type
                $titles = $assignmentData[$type][$subjectName] ?? $assignmentData[$type]['الرياضيات'] ?? ['واجب عام'];

                $title = $titles[array_rand($titles)];
                $description = $this->generateDescription($type, $title);

                // Find a future class session for due date (within next 2 weeks)
                $dueSession = $this->findFutureClassSession($classSession, $sections);

                // Check if assignment already exists for this session
                $existingAssignment = Assignment::where('assigned_session_id', $classSession->id)
                    ->where('type', $type)
                    ->first();

                if (!$existingAssignment) {
                    Assignment::create([
                        'assigned_session_id' => $classSession->id,
                        'due_session_id' => $dueSession ? $dueSession->id : null,
                        'type' => $type,
                        'title' => $title,
                        'description' => $description,
                        'photo' => null, // Could be enhanced with actual images
                        'subject_id' => $classSession->subject_id,
                        'section_id' => $classSession->section_id,
                        'created_by' => 1,
                    ]);
                }
            }
        }
    }

    /**
     * Generate a description for the assignment
     */
    private function generateDescription(string $type, string $title): string
    {
        $descriptions = [
            'homework' => [
                'أكمل هذا الواجب بدقة وقدمه في الوقت المحدد. تأكد من إظهار جميع عملك واتباع التعليمات بعناية.',
                'سيساعدك هذا الواجب المنزلي في تعزيز المفاهيم التي تعلمناها في الفصل. خذ وقتك وقدم أفضل عمل.',
                'يرجى إكمال هذا الواجب بشكل مستقل. إذا كان لديك أسئلة، لا تتردد في السؤال خلال ساعات العمل.',
                'تم تصميم هذا الواجب المنزلي لممارسة المهارات التي غطيناها اليوم. قدم عملك بشكل منظم وفي الوقت المحدد.',
                'أكمل جميع المسائل وأظهر عملك بوضوح. سيتم تقييم هذا الواجب من حيث الدقة والاكتمال.'
            ],
            'oral' => [
                'أعد عرضاً تقديمياً لمدة 3-5 دقائق حول الموضوع المحدد. تدرب على العرض وكن مستعداً للإجابة على الأسئلة.',
                'سيختبر هذا العرض الشفهي فهمك ومهاراتك في التواصل. أعد جيداً وتحدث بوضوح.',
                'ستقدم نتائجك للفصل. تأكد من تنظيم أفكارك والتدرب على العرض.',
                'سيساعدك هذا الواجب الشفهي في تطوير مهاراتك في الخطابة العامة. أعد موادك وتدرب على العرض.',
                'كن مستعداً لتقديم عملك شفهياً. ركز على التواصل الواضح والعرض الواثق.'
            ],
            'quiz' => [
                'سيختبر هذا الاختبار فهمك للمادة التي غطيناها. راجع ملاحظاتك وكن مستعداً.',
                'سيشمل الاختبار أسئلة متعددة الخيارات وإجابات قصيرة وحل مسائل. ادرس الفصول ذات الصلة.',
                'سيقيم هذا الاختبار معرفتك بالموضوع. تأكد من مراجعة جميع المواد المغطاة.',
                'سيكون الاختبار شاملاً ويختبر المعرفة النظرية والعملية.',
                'أعد لهذا الاختبار من خلال مراجعة ملاحظاتك وإكمال المسائل التدريبية.'
            ]
        ];

        $typeDescriptions = $descriptions[$type] ?? $descriptions['homework'];
        return $typeDescriptions[array_rand($typeDescriptions)];
    }

    /**
     * Find a future class session for the due date
     */
    private function findFutureClassSession($assignedSession, $sections)
    {
        // Find a class session in the same section within the next 2 weeks
        $futureSessions = ClassSession::where('section_id', $assignedSession->section_id)
            ->where('id', '>', $assignedSession->id)
            ->whereHas('schoolDay', function($query) use ($assignedSession) {
                $query->where('date', '>', $assignedSession->schoolDay->date)
                      ->where('date', '<=', $assignedSession->schoolDay->date->addDays(14));
            })
            ->orderBy('id')
            ->limit(5)
            ->get();

        return $futureSessions->isNotEmpty() ? $futureSessions->random() : null;
    }
}
