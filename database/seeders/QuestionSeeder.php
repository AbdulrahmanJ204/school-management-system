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
            'choices' => json_encode(["1", "2", "3", "4"]),
            'right_choice' => 3, // index of "4"
            'hint' => "It’s basic math.",
            'order' => 1,
        ]);

        Question::create([
            'quiz_id' => 1,
            'question_text' => json_encode([
                "ops" => [
                    ["insert" => "Which planet is known as the Red Planet?"]
                ]
            ]),
            'choices' => json_encode(["Earth", "Venus", "Mars", "Jupiter"]),
            'right_choice' => 2, // "Mars"
            'hint' => "It’s the 4th planet from the Sun.",
            'order' => 2,
        ]);

        Question::create([
            'quiz_id' => 1,
            'question_text' => json_encode([
                "ops" => [
                    ["insert" => "Who wrote 'Romeo and Juliet'?"]
                ]
            ]),
            'choices' => json_encode(["Charles Dickens", "Shakespeare", "Homer", "Tolstoy"]),
            'right_choice' => 1, // "Shakespeare"
            'hint' => "English playwright from the 16th century.",
            'order' => 3,
        ]);
    }
}
