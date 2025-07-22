<?php

namespace Database\Seeders;

use App\Models\Grade;
use App\Models\News;
use App\Models\NewsTarget;
use App\Models\SchoolDay;
use App\Models\Section;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some school days to associate with news
        $schoolDays = SchoolDay::limit(10)->get();

        if ($schoolDays->isEmpty()) {
            // If no school days exist, create some basic news without school_day_id
            return;
        }

        $news = [
            [
                'title' => 'Welcome Back to School!',
                'content' => json_encode([
                    'ops' => [
                        [
                            'insert' => 'Welcome Back Students!'
                        ],
                        [
                            'attributes' => ['header' => 1],
                            'insert' => "\n"
                        ],
                        [
                            'insert' => 'We are excited to welcome all students back for the new academic year. Please check your schedules and report to your assigned classrooms.'
                        ],
                        [
                            'insert' => "\n\n"
                        ],
                        [
                            'insert' => 'Important reminders:'
                        ],
                        [
                            'attributes' => ['bold' => true],
                            'insert' => "\n"
                        ],
                        [
                            'insert' => '• Check your class schedule\n• Bring required materials\n• Report to homeroom by 8:00 AM'
                        ],
                        [
                            'insert' => "\n"
                        ]
                    ]
                ]),
                'school_day_id' => $schoolDays->first()->id,
                'photo' => null,
                'created_by' => 1,
            ],
            [
                'title' => 'Parent-Teacher Conference',
                'content' => json_encode([
                    'ops' => [
                        [
                            'insert' => 'Parent-Teacher Conference Announcement'
                        ],
                        [
                            'attributes' => ['header' => 2],
                            'insert' => "\n"
                        ],
                        [
                            'insert' => 'Parent-teacher conferences will be held next week. Please schedule your appointment through the school portal.'
                        ],
                        [
                            'insert' => "\n\n"
                        ],
                        [
                            'insert' => 'Conference Details:'
                        ],
                        [
                            'attributes' => ['bold' => true],
                            'insert' => "\n"
                        ],
                        [
                            'insert' => '• Date: March 15-16, 2024\n• Time: 2:00 PM - 6:00 PM\n• Location: Individual classrooms\n• Duration: 15 minutes per appointment'
                        ],
                        [
                            'insert' => "\n"
                        ]
                    ]
                ]),
                'school_day_id' => $schoolDays->skip(1)->first()->id ?? $schoolDays->first()->id,
                'photo' => null,
                'created_by' => 1,
            ],
            [
                'title' => 'Science Fair Announcement',
                'content' => json_encode([
                    'ops' => [
                        [
                            'insert' => 'Annual Science Fair 2024'
                        ],
                        [
                            'attributes' => ['header' => 1],
                            'insert' => "\n"
                        ],
                        [
                            'insert' => 'The annual science fair will take place in two weeks. Students are encouraged to start preparing their projects.'
                        ],
                        [
                            'insert' => "\n\n"
                        ],
                        [
                            'insert' => 'Project Categories:'
                        ],
                        [
                            'attributes' => ['bold' => true],
                            'insert' => "\n"
                        ],
                        [
                            'insert' => '1. Biology & Life Sciences\n2. Chemistry & Physical Sciences\n3. Engineering & Technology\n4. Environmental Science'
                        ],
                        [
                            'insert' => "\n\n"
                        ],
                        [
                            'insert' => 'Submission deadline: '
                        ],
                        [
                            'attributes' => ['bold' => true, 'underline' => true],
                            'insert' => 'March 30, 2024'
                        ],
                        [
                            'insert' => "\n"
                        ]
                    ]
                ]),
                'school_day_id' => $schoolDays->skip(2)->first()->id ?? $schoolDays->first()->id,
                'photo' => null,
                'created_by' => 1,
            ],
            [
                'title' => 'Sports Day Event',
                'content' => json_encode([
                    'ops' => [
                        [
                            'insert' => 'Annual Sports Day 2024'
                        ],
                        [
                            'attributes' => ['header' => 1],
                            'insert' => "\n"
                        ],
                        [
                            'insert' => 'Our annual sports day will be held next month. Registration forms are available at the main office.'
                        ],
                        [
                            'insert' => "\n\n"
                        ],
                        [
                            'insert' => 'Event Schedule:'
                        ],
                        [
                            'attributes' => ['bold' => true],
                            'insert' => "\n"
                        ],
                        [
                            'insert' => '• Track Events: 9:00 AM - 12:00 PM\n• Field Events: 1:00 PM - 4:00 PM\n• Team Sports: 10:00 AM - 3:00 PM'
                        ],
                        [
                            'insert' => "\n\n"
                        ],
                        [
                            'insert' => 'All students are encouraged to participate!'
                        ],
                        [
                            'attributes' => ['italic' => true],
                            'insert' => "\n"
                        ]
                    ]
                ]),
                'school_day_id' => $schoolDays->skip(3)->first()->id ?? $schoolDays->first()->id,
                'photo' => null,
                'created_by' => 1,
            ],
            [
                'title' => 'Mid-Term Exam Schedule',
                'content' => json_encode([
                    'ops' => [
                        [
                            'insert' => 'Mid-Term Examination Schedule'
                        ],
                        [
                            'attributes' => ['header' => 2],
                            'insert' => "\n"
                        ],
                        [
                            'insert' => 'Mid-term examinations will begin next week. Please review the exam schedule posted on the notice board.'
                        ],
                        [
                            'insert' => "\n\n"
                        ],
                        [
                            'insert' => 'Important Exam Information:'
                        ],
                        [
                            'attributes' => ['bold' => true],
                            'insert' => "\n"
                        ],
                        [
                            'insert' => '• Exam Period: April 1-5, 2024\n• Duration: 2 hours per subject\n• Start Time: 9:00 AM\n• Location: Main Examination Hall'
                        ],
                        [
                            'insert' => "\n\n"
                        ],
                        [
                            'insert' => 'Note: '
                        ],
                        [
                            'attributes' => ['bold' => true],
                            'insert' => 'Students must arrive 30 minutes before exam time'
                        ],
                        [
                            'insert' => "\n"
                        ]
                    ]
                ]),
                'school_day_id' => $schoolDays->where('type', 'exam')->first()->id ?? $schoolDays->first()->id,
                'photo' => null,
                'created_by' => 1,
            ],
        ];

        foreach ($news as $newsItem) {
            $createdNews = News::create($newsItem);

            // Create news targets for demonstration
            $this->createNewsTargets($createdNews->id);
        }
    }

    private function createNewsTargets($newsId)
    {
        $grades = Grade::all();

        // Create different targeting scenarios
        switch ($newsId % 4) {
            case 1: // Target all grades
                NewsTarget::create([
                    'news_id' => $newsId,
                    'grade_id' => null,
                    'section_id' => null,
                    'created_by' => 1,
                ]);
                break;

            case 2: // Target specific grade (e.g., Grade 5)
                NewsTarget::create([
                    'news_id' => $newsId,
                    'grade_id' => 5,
                    'section_id' => null,
                    'created_by' => 1,
                ]);
                break;

            case 3: // Target specific section (e.g., Grade 3, Section A)
                $section = Section::where('grade_id', 3)->where('title', 'A')->first();
                if ($section) {
                    NewsTarget::create([
                        'news_id' => $newsId,
                        'grade_id' => null,
                        'section_id' => $section->id,
                        'created_by' => 1,
                    ]);
                }
                break;

            default: // Target high school grades (9-12)
                $highSchoolGrades = Grade::whereIn('id', [9, 10, 11, 12])->get();
                foreach ($highSchoolGrades as $grade) {
                    NewsTarget::create([
                        'news_id' => $newsId,
                        'grade_id' => $grade->id,
                        'section_id' => null,
                        'created_by' => 1,
                    ]);
                }
                break;
        }
    }
}
