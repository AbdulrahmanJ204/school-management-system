<?php

namespace Database\Seeders;

use App\Models\Quiz;
use App\Models\QuizTarget;
use App\Models\Question;
use App\Models\Teacher;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Semester;
use App\Models\Year;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teachers = Teacher::all();
        $sections = Section::all();
        $subjects = Subject::all();
        $semesters = Semester::all();
        $years = Year::all();

        if ($teachers->isEmpty() || $sections->isEmpty() || $subjects->isEmpty()) {
            $this->command->warn('Teachers, Sections, or Subjects not found. Please run the respective seeders first.');
            return;
        }

        if ($semesters->isEmpty()) {
            $this->command->warn('No semesters found. Please run SemesterSeeder first.');
            return;
        }

        // Arabic quiz names for different subjects
        $quizNames = [
            'اختبار الرياضيات النصفي',
            'اختبار العلوم الشهري',
            'اختبار اللغة العربية',
            'اختبار اللغة الإنجليزية',
            'اختبار الدراسات الاجتماعية',
            'اختبار التربية الإسلامية',
            'اختبار الحاسوب',
            'اختبار الفنون',
            'اختبار التربية البدنية',
            'اختبار الموسيقى',
            'اختبار العلوم التطبيقية',
            'اختبار الرياضيات التطبيقية',
            'اختبار اللغة العربية التطبيقية',
            'اختبار العلوم النظرية',
            'اختبار الرياضيات النظرية'
        ];

        // Arabic question templates for different subjects
        $questionTemplates = [
            'math' => [
                'ما هو ناتج العملية الحسابية؟',
                'أي من الأعداد التالية هو الأكبر؟',
                'كم عدد الأضلاع في المربع؟',
                'ما هو محيط الدائرة؟',
                'أي من الأشكال التالية له 3 أضلاع؟'
            ],
            'science' => [
                'أي من الكواكب التالية هو الأقرب للشمس؟',
                'ما هو العنصر الكيميائي الأكثر وفرة في القشرة الأرضية؟',
                'أي من الحيوانات التالية هو من الثدييات؟',
                'ما هو الجزء المسؤول عن البصر في العين؟',
                'أي من النباتات التالية له أوراق إبرية؟'
            ],
            'arabic' => [
                'أي من الكلمات التالية مكتوبة بشكل صحيح؟',
                'ما هو جمع كلمة "كتاب"؟',
                'أي من الجمل التالية صحيحة نحوياً؟',
                'ما هو معنى كلمة "مدرسة"؟',
                'أي من الأفعال التالية ماضي؟'
            ],
            'english' => [
                'What is the correct spelling?',
                'Which word is a synonym for "happy"?',
                'Choose the correct verb form:',
                'What is the plural of "child"?',
                'Which sentence is grammatically correct?'
            ],
            'social' => [
                'أي من المدن التالية هي عاصمة مصر؟',
                'في أي عام تأسست المملكة العربية السعودية؟',
                'أي من البحار التالية يقع في الشرق الأوسط؟',
                'ما هو اسم أول خليفة في الإسلام؟',
                'أي من الدول التالية تقع في قارة أفريقيا؟'
            ]
        ];



        $teacherIndex = 0;
        $quizCounter = 0;

        foreach ($sections as $section) {
            foreach ($subjects as $subject) {
                // Get a teacher for this section-subject combination
                $teacher = $teachers[$teacherIndex % $teachers->count()];
                
                // Get semester for this section's grade year, or use first available semester
                $semester = null;
                if ($section->grade && $section->grade->year_id) {
                    $semester = $semesters->where('year_id', $section->grade->year_id)->first();
                }
                
                if (!$semester) {
                    $semester = $semesters->first();
                }
                
                // Create quiz
                $quiz = Quiz::create([
                    'name' => $quizNames[$quizCounter % count($quizNames)],
                    'full_score' => rand(50, 100),
                    'is_active' => rand(0, 1),
                    'created_by' => $teacher->user_id,
                ]);

                // Create quiz target
                QuizTarget::create([
                    'quiz_id' => $quiz->id,
                    'grade_id' => $section->grade_id,
                    'subject_id' => $subject->id,
                    'section_id' => $section->id,
                    'semester_id' => $semester->id,
                ]);

                // Create questions for this quiz
                $this->createQuestionsForQuiz($quiz, $subject, $questionTemplates);

                $teacherIndex++;
                $quizCounter++;
            }
        }

        $this->command->info("Created {$quizCounter} quizzes for all sections and teachers.");
    }

    /**
     * Create questions for a specific quiz
     */
    private function createQuestionsForQuiz($quiz, $subject, $questionTemplates)
    {
        // Determine subject type for question templates
        $subjectType = $this->getSubjectType($subject->name);
        $templates = $questionTemplates[$subjectType] ?? $questionTemplates['math'];

        // Create 5-10 questions per quiz
        $questionCount = rand(5, 10);

        for ($i = 1; $i <= $questionCount; $i++) {
            $template = $templates[($i - 1) % count($templates)];
            
            // Create question text with subject-specific content
            $questionText = $this->generateQuestionText($template, $subject, $i);
            
            // Create choices
            $choices = $this->generateChoices($subject, $i);
            
            Question::create([
                'quiz_id' => $quiz->id,
                'question_text' => json_encode([
                    "ops" => [
                        ["insert" => $questionText]
                    ]
                ]),
                'choices' => json_encode([
                    'ops' => array_map(function($choice) {
                        return ['insert' => $choice];
                    }, $choices)
                ]),
                'choices_count' => count($choices),
                'right_choice' => rand(0, count($choices) - 1),
                'hint' => json_encode([
                    "ops" => [
                        ["insert" => "تلميح: راجع الدرس بعناية"]
                    ]
                ]),
                'order' => $i,
            ]);
        }
    }

    /**
     * Determine subject type for question templates
     */
    private function getSubjectType($subjectName)
    {
        $subjectName = strtolower($subjectName);
        
        if (str_contains($subjectName, 'رياضيات') || str_contains($subjectName, 'math')) {
            return 'math';
        } elseif (str_contains($subjectName, 'علوم') || str_contains($subjectName, 'science')) {
            return 'science';
        } elseif (str_contains($subjectName, 'عربية') || str_contains($subjectName, 'arabic')) {
            return 'arabic';
        } elseif (str_contains($subjectName, 'إنجليزية') || str_contains($subjectName, 'english')) {
            return 'english';
        } elseif (str_contains($subjectName, 'اجتماعية') || str_contains($subjectName, 'social')) {
            return 'social';
        }
        
        return 'math'; // default
    }

    /**
     * Generate question text with subject-specific content
     */
    private function generateQuestionText($template, $subject, $questionNumber)
    {
        $subjectName = $subject->name;
        
        // Add subject-specific context to questions
        switch ($this->getSubjectType($subjectName)) {
            case 'math':
                $numbers = [rand(1, 100), rand(1, 100), rand(1, 100)];
                return str_replace('العملية الحسابية', "{$numbers[0]} + {$numbers[1]} × {$numbers[2]}", $template);
            
            case 'science':
                $elements = ['الأكسجين', 'الكربون', 'الحديد', 'النحاس'];
                return str_replace('العنصر الكيميائي', $elements[array_rand($elements)], $template);
            
            case 'arabic':
                $words = ['كتاب', 'قلم', 'مدرسة', 'طالب'];
                return str_replace('كلمة', $words[array_rand($words)], $template);
            
            default:
                return $template;
        }
    }

    /**
     * Generate choices for questions
     */
    private function generateChoices($subject, $questionNumber)
    {
        $subjectType = $this->getSubjectType($subject->name);
        
        switch ($subjectType) {
            case 'math':
                $baseNumber = rand(1, 20);
                return [
                    $baseNumber - 2,
                    $baseNumber - 1,
                    $baseNumber,
                    $baseNumber + 1
                ];
            
            case 'science':
                return ['الخيار الأول', 'الخيار الثاني', 'الخيار الثالث', 'الخيار الرابع'];
            
            case 'arabic':
                return ['أولاً', 'ثانياً', 'ثالثاً', 'رابعاً'];
            
            case 'english':
                return ['Option A', 'Option B', 'Option C', 'Option D'];
            
            default:
                return ['أولاً', 'ثانياً', 'ثالثاً', 'رابعاً'];
        }
    }
}
