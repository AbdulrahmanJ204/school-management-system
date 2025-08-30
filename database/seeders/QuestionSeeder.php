<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all existing quizzes
        $quizzes = Quiz::all();
        
        if ($quizzes->isEmpty()) {
            $this->command->warn('No quizzes found. Please run QuizSeeder first.');
            return;
        }

        // Create additional sample questions for existing quizzes if needed
        foreach ($quizzes as $quiz) {
            $existingQuestions = $quiz->questions()->count();
            
            // If quiz has less than 5 questions, add some sample ones
            if ($existingQuestions < 5) {
                $this->createSampleQuestions($quiz, 5 - $existingQuestions);
            }
        }

        $this->command->info('QuestionSeeder completed. Additional questions added where needed.');
    }

    /**
     * Create sample questions for a quiz
     */
    private function createSampleQuestions($quiz, $count)
    {
        $sampleQuestions = [
            [
                'question_text' => 'ما هو ناتج 5 × 6؟',
                'choices' => ['25', '30', '35', '40'],
                'right_choice' => 1,
                'hint' => 'استخدم جدول الضرب'
            ],
            [
                'question_text' => 'أي من الألوان التالية هو لون أساسي؟',
                'choices' => ['الأحمر', 'الأخضر', 'البرتقالي', 'البنفسجي'],
                'right_choice' => 0,
                'hint' => 'الألوان الأساسية هي الأحمر والأزرق والأصفر'
            ],
            [
                'question_text' => 'كم عدد أيام الأسبوع؟',
                'choices' => ['5 أيام', '6 أيام', '7 أيام', '8 أيام'],
                'right_choice' => 2,
                'hint' => 'الأسبوع يحتوي على سبعة أيام'
            ],
            [
                'question_text' => 'أي من الحيوانات التالية هو من الطيور؟',
                'choices' => ['الأسد', 'النمر', 'العصفور', 'الكلب'],
                'right_choice' => 2,
                'hint' => 'العصفور له أجنحة وريش'
            ],
            [
                'question_text' => 'ما هو اسم أكبر قارة في العالم؟',
                'choices' => ['أفريقيا', 'أوروبا', 'آسيا', 'أمريكا الشمالية'],
                'right_choice' => 2,
                'hint' => 'آسيا هي أكبر قارة من حيث المساحة'
            ]
        ];

        $startOrder = $quiz->questions()->max('order') + 1;

        for ($i = 0; $i < min($count, count($sampleQuestions)); $i++) {
            $question = $sampleQuestions[$i];
            
            Question::create([
                'quiz_id' => $quiz->id,
                'question_text' => json_encode([
                    "ops" => [
                        ["insert" => $question['question_text']]
                    ]
                ]),
                'choices' => json_encode([
                    'ops' => array_map(function($choice) {
                        return ['insert' => $choice];
                    }, $question['choices'])
                ]),
                'choices_count' => count($question['choices']),
                'right_choice' => $question['right_choice'],
                'hint' => json_encode([
                    "ops" => [
                        ["insert" => $question['hint']]
                    ]
                ]),
                'order' => $startOrder + $i,
            ]);
        }
    }
}
