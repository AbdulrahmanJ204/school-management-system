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
                'content' => 'We are excited to welcome all students back for the new academic year. Please check your schedules and report to your assigned classrooms.',
                'school_day_id' => $schoolDays->first()->id,
                'photo' => null,
                'created_by' => 1,
            ],
            [
                'title' => 'Parent-Teacher Conference',
                'content' => 'Parent-teacher conferences will be held next week. Please schedule your appointment through the school portal.',
                'school_day_id' => $schoolDays->skip(1)->first()->id ?? $schoolDays->first()->id,
                'photo' => null,
                'created_by' => 1,
            ],
            [
                'title' => 'Science Fair Announcement',
                'content' => 'The annual science fair will take place in two weeks. Students are encouraged to start preparing their projects.',
                'school_day_id' => $schoolDays->skip(2)->first()->id ?? $schoolDays->first()->id,
                'photo' => null,
                'created_by' => 1,
            ],
            [
                'title' => 'Sports Day Event',
                'content' => 'Our annual sports day will be held next month. Registration forms are available at the main office.',
                'school_day_id' => $schoolDays->skip(3)->first()->id ?? $schoolDays->first()->id,
                'photo' => null,
                'created_by' => 1,
            ],
            [
                'title' => 'Mid-Term Exam Schedule',
                'content' => 'Mid-term examinations will begin next week. Please review the exam schedule posted on the notice board.',
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
