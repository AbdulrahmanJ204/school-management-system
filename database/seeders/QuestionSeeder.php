<?php

namespace Database\Seeders;

use App\Models\Question;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Question::create([
            'quiz_id' => 1,
            'question_text' => json_encode([
                "ops" => [
                    ["insert" => "What is 2 + 2?"]
                ]
            ]),
            'choices'            => json_encode([
                'ops' => [
                    ['insert' => "1\n"],
                    ['insert' => "2\n"],
                    ['insert' => "3\n"],
                    ['insert' => "4\n"],
                ]
            ]),
            'choices_count' => 4,
            'right_choice' => 3, // index of "4"
            'hint' => json_encode([
                "ops" => [
                    ["insert" => "basic"]
                ]
            ]),
            'order' => 1,
        ]);

        Question::create([
            'quiz_id' => 1,
            'question_text' => json_encode([
                "ops" => [
                    ["insert" => "Which planet is known as the Red Planet?"]
                ]
            ]),
            'choices'            => json_encode([
                'ops' => [
                    ['insert' => "1\n"],
                    ['insert' => "2\n"],
                    ['insert' => "3\n"],
                    ['insert' => "4\n"],
                ]
            ]),
            'choices_count' => 4,
            'right_choice' => 2, // "Mars"
            'hint' => json_encode([
                "ops" => [
                    ["insert" => "basic"]
                ]
            ]),
            'order' => 2,
        ]);

        Question::create([
            'quiz_id' => 1,
            'question_text' => json_encode([
                "ops" => [
                    ["insert" => "Who wrote 'Romeo and Juliet'?"]
                ]
            ]),
            'choices'            => json_encode([
                'ops' => [
                    ['insert' => "1\n"],
                    ['insert' => "2\n"],
                    ['insert' => "3\n"],
                    ['insert' => "4\n"],
                ]
            ]),
            'choices_count' => 4,
            'right_choice' => 1, // "Shakespeare"
            'hint' => json_encode([
                "ops" => [
                    ["insert" => "basic"]
                ]
            ]),
            'order' => 3,
        ]);
    }
}
