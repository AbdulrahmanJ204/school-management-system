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
        $assignmentTypes = ['homework', 'oral', 'quiz', 'project'];

        // Sample assignment data for different subjects
        $assignmentData = [
            'homework' => [
                'Mathematics' => [
                    'Solve quadratic equations problems 1-10',
                    'Complete the geometry worksheet',
                    'Practice algebraic expressions',
                    'Review chapter 5 exercises',
                    'Solve word problems from textbook'
                ],
                'Science' => [
                    'Read chapter 3 and answer questions',
                    'Complete the lab report',
                    'Research on photosynthesis',
                    'Write a summary of the experiment',
                    'Prepare for next lab session'
                ],
                'English' => [
                    'Write a 500-word essay on your favorite book',
                    'Complete grammar exercises',
                    'Read the assigned novel chapter',
                    'Practice vocabulary words',
                    'Write a creative story'
                ],
                'History' => [
                    'Research on ancient civilizations',
                    'Write a report on historical events',
                    'Complete timeline assignment',
                    'Study for upcoming test',
                    'Read primary source documents'
                ]
            ],
            'oral' => [
                'English' => [
                    'Present your book report',
                    'Practice pronunciation exercises',
                    'Give a speech on current events',
                    'Participate in group discussion',
                    'Present your research findings'
                ],
                'Science' => [
                    'Present your experiment results',
                    'Explain scientific concepts',
                    'Discuss environmental issues',
                    'Present your research project',
                    'Explain lab procedures'
                ],
                'History' => [
                    'Present historical figure biography',
                    'Discuss historical events',
                    'Present your research findings',
                    'Explain historical significance',
                    'Participate in debate'
                ]
            ],
            'quiz' => [
                'Mathematics' => [
                    'Algebra Quiz - Chapter 3',
                    'Geometry Quiz - Triangles',
                    'Calculus Quiz - Derivatives',
                    'Statistics Quiz - Probability',
                    'Trigonometry Quiz - Unit Circle'
                ],
                'Science' => [
                    'Biology Quiz - Cell Structure',
                    'Chemistry Quiz - Chemical Reactions',
                    'Physics Quiz - Forces and Motion',
                    'Earth Science Quiz - Plate Tectonics',
                    'Environmental Science Quiz - Ecosystems'
                ],
                'English' => [
                    'Grammar Quiz - Parts of Speech',
                    'Vocabulary Quiz - Unit 5',
                    'Literature Quiz - Shakespeare',
                    'Writing Quiz - Essay Structure',
                    'Reading Comprehension Quiz'
                ]
            ],
            'project' => [
                'Science' => [
                    'Design and build a simple machine',
                    'Create a model of the solar system',
                    'Conduct a science fair project',
                    'Build an ecosystem in a bottle',
                    'Create a weather station'
                ],
                'Mathematics' => [
                    'Create a mathematical model',
                    'Design a geometric art project',
                    'Build a scale model using math',
                    'Create a statistical analysis project',
                    'Design a mathematical game'
                ],
                'English' => [
                    'Create a digital storytelling project',
                    'Design a magazine layout',
                    'Produce a short film',
                    'Create a poetry anthology',
                    'Design a book cover'
                ],
                'History' => [
                    'Create a historical timeline',
                    'Build a model of ancient architecture',
                    'Design a museum exhibit',
                    'Create a documentary',
                    'Build a historical diorama'
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
                $titles = $assignmentData[$type][$subjectName] ?? $assignmentData[$type]['Mathematics'] ?? ['General Assignment'];

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
                'Complete this assignment thoroughly and submit it on time. Make sure to show all your work and follow the instructions carefully.',
                'This homework will help reinforce the concepts we learned in class. Take your time and do your best work.',
                'Please complete this assignment independently. If you have questions, feel free to ask during office hours.',
                'This homework is designed to practice the skills we covered today. Submit your work neatly and on time.',
                'Complete all problems and show your work clearly. This assignment will be graded for accuracy and completeness.'
            ],
            'oral' => [
                'Prepare a 3-5 minute presentation on the given topic. Practice your delivery and be ready to answer questions.',
                'This oral presentation will test your understanding and communication skills. Prepare thoroughly and speak clearly.',
                'You will present your findings to the class. Make sure to organize your thoughts and practice your delivery.',
                'This oral assignment will help develop your public speaking skills. Prepare your material and practice your presentation.',
                'Be ready to present your work orally. Focus on clear communication and confident delivery.'
            ],
            'quiz' => [
                'This quiz will test your understanding of the material we have covered. Review your notes and be prepared.',
                'The quiz will include multiple choice, short answer, and problem-solving questions. Study the relevant chapters.',
                'This assessment will evaluate your knowledge of the subject matter. Make sure to review all covered material.',
                'The quiz will be comprehensive and will test both theoretical and practical knowledge.',
                'Prepare for this quiz by reviewing your notes and completing the practice problems.'
            ],
            'project' => [
                'This project will allow you to demonstrate your creativity and understanding of the subject. Plan your work carefully.',
                'Work on this project individually or in groups as assigned. Be creative and thorough in your approach.',
                'This project will showcase your skills and knowledge. Take your time and produce quality work.',
                'Plan and execute this project carefully. It will be a significant part of your grade for this unit.',
                'This project will require research, planning, and presentation. Start early and work consistently.'
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

        return $futureSessions->random();
    }
}
