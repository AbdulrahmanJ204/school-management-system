<?php

namespace Database\Seeders;

use App\Models\Grade;
use App\Models\News;
use App\Models\NewsTarget;
use App\Models\Section;
use App\Models\Year;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $firstYearStartDate = Year::find(1)->start_date;
        $secondYearStartDate = Year::find(2)->start_date;
        $thirdYearStartDate = Year::find(3)->start_date;
        $firstYearEndDate = Year::find(1)->end_date;
        $secondYearEndDate = Year::find(2)->end_date;
        $thirdYearEndDate = Year::find(3)->end_date;
        $dates = [
            $firstYearStartDate->addDays(5),
            $secondYearStartDate->addDays(5),
            $thirdYearStartDate->addDays(5),
            $firstYearEndDate->subDays(10),
            $secondYearEndDate->subDays(10),
            $thirdYearEndDate->subDays(10),
        ];

        $news = [
            [
                'title' => 'مرحباً بعودتك إلى المدرسة!',
                'content' => json_encode([
                    'ops' => [
                        [
                            'insert' => 'مرحباً بعودة الطلاب!'
                        ],
                        [
                            'attributes' => ['header' => 1],
                            'insert' => "\n"
                        ],
                        [
                            'insert' => 'نحن متحمسون لترحيب جميع الطلاب بالعام الدراسي الجديد. يرجى التحقق من جداولك الدراسية والتوجه إلى الفصول الدراسية المخصصة لك.'
                        ],
                        [
                            'insert' => "\n\n"
                        ],
                        [
                            'insert' => 'تذكيرات مهمة:'
                        ],
                        [
                            'attributes' => ['bold' => true],
                            'insert' => "\n"
                        ],
                        [
                            'insert' => '• تحقق من جدولك الدراسي\n• أحضر المواد المطلوبة\n• توجه إلى الفصل الدراسي بحلول الساعة 8:00 صباحاً'
                        ],
                        [
                            'insert' => "\n"
                        ]
                    ]
                ]),
                'publish_date' => $dates[array_rand($dates)],
                'photo' => null,
                'created_by' => 1,
            ],
            [
                'title' => 'مؤتمر الآباء والمعلمين',
                'content' => json_encode([
                    'ops' => [
                        [
                            'insert' => 'إعلان مؤتمر الآباء والمعلمين'
                        ],
                        [
                            'attributes' => ['header' => 2],
                            'insert' => "\n"
                        ],
                        [
                            'insert' => 'سيتم عقد مؤتمرات الآباء والمعلمين الأسبوع القادم. يرجى تحديد موعدك من خلال بوابة المدرسة.'
                        ],
                        [
                            'insert' => "\n\n"
                        ],
                        [
                            'insert' => 'تفاصيل المؤتمر:'
                        ],
                        [
                            'attributes' => ['bold' => true],
                            'insert' => "\n"
                        ],
                        [
                            'insert' => '• التاريخ: 15-16 مارس 2024\n• الوقت: 2:00 مساءً - 6:00 مساءً\n• المكان: الفصول الدراسية الفردية\n• المدة: 15 دقيقة لكل موعد'
                        ],
                        [
                            'insert' => "\n"
                        ]
                    ]
                ]),
                'publish_date' => $dates[array_rand($dates)],
                'photo' => null,
                'created_by' => 1,
            ],
            [
                'title' => 'إعلان معرض العلوم',
                'content' => json_encode([
                    'ops' => [
                        [
                            'insert' => 'معرض العلوم السنوي 2024'
                        ],
                        [
                            'attributes' => ['header' => 1],
                            'insert' => "\n"
                        ],
                        [
                            'insert' => 'سيتم عقد معرض العلوم في أسبوعين. يرجى إطلاع الطلاب على المشاريع التي يجب أن يبدأوا في تحضيرها.'
                        ],
                        [
                            'insert' => "\n\n"
                        ],
                        [
                            'insert' => 'فئات المشاريع:'
                        ],
                        [
                            'attributes' => ['bold' => true],
                            'insert' => "\n"
                        ],
                        [
                            'insert' => '1. الأحياء والعلوم الحية\n2. الكيمياء والعلوم الفيزيائية\n3. الهندسة والتكنولوجيا\n4. علوم البيئة'
                        ],
                        [
                            'insert' => "\n\n"
                        ],
                        [
                            'insert' => 'موعد التسليم: '
                        ],
                        [
                            'attributes' => ['bold' => true, 'underline' => true],
                            'insert' => '30 مارس 2024'
                        ],
                        [
                            'insert' => "\n"
                        ]
                    ]
                ]),
                'publish_date' => $dates[array_rand($dates)],
                'photo' => null,
                'created_by' => 1,
            ],
            [
                'title' => 'حدث الرياضة',
                'content' => json_encode([
                    'ops' => [
                        [
                            'insert' => 'حدث الرياضة السنوي 2024'
                        ],
                        [
                            'attributes' => ['header' => 1],
                            'insert' => "\n"
                        ],
                        [
                            'insert' => 'سيتم عقد حدث الرياضة السنوي في الشهر القادم. إشعارات التسجيل متوفرة في المكتب الرئيسي.'
                        ],
                        [
                            'insert' => "\n\n"
                        ],
                        [
                            'insert' => 'جدول الحدث:'
                        ],
                        [
                            'attributes' => ['bold' => true],
                            'insert' => "\n"
                        ],
                        [
                            'insert' => '• الأحداث الرياضية: 9:00 صباحاً - 12:00 ظهراً\n• الأحداث الميدانية: 1:00 ظهراً - 4:00 عصراً\n• الرياضات الفردية: 10:00 صباحاً - 3:00 عصراً'
                        ],
                        [
                            'insert' => "\n\n"
                        ],
                        [
                            'insert' => 'يرجى تشجيع جميع الطلاب على المشاركة!'
                        ],
                        [
                            'attributes' => ['italic' => true],
                            'insert' => "\n"
                        ]
                    ]
                ]),
                'publish_date' => $dates[array_rand($dates)],
                'photo' => null,
                'created_by' => 1,
            ],
            [
                'title' => 'جدول امتحان النصف الأول',
                'content' => json_encode([
                    'ops' => [
                        [
                            'insert' => 'جدول امتحان النصف الأول'
                        ],
                        [
                            'attributes' => ['header' => 2],
                            'insert' => "\n"
                        ],
                        [
                            'insert' => 'سيبدأ امتحانات النصف الأول في الأسبوع القادم. يرجى مراجعة جدول الامتحانات المعلق على اللوحة الإعلانية.'
                        ],
                        [
                            'insert' => "\n\n"
                        ],
                        [
                            'insert' => 'معلومات حول الامتحان:'
                        ],
                        [
                            'attributes' => ['bold' => true],
                            'insert' => "\n"
                        ],
                        [
                            'insert' => '• فترة الامتحان: 1-5 أبريل 2024\n• المدة: 2 ساعة لكل مادة\n• الوقت البدء: 9:00 صباحاً\n• المكان: صالة الامتحانات الرئيسية'
                        ],
                        [
                            'insert' => "\n\n"
                        ],
                        [
                            'insert' => 'ملاحظة: '
                        ],
                        [
                            'attributes' => ['bold' => true],
                            'insert' => 'يجب أن يصل الطلاب 30 دقيقة قبل وقت الامتحان'
                        ],
                        [
                            'insert' => "\n"
                        ]
                    ]
                ]),
                'publish_date' => $dates[array_rand($dates)],
                'photo' => null,
                'created_by' => 1,
            ],
            [
                'title' => 'مرحباً بعودتك إلى المدرسة!',
                'content' => json_encode([
                    'ops' => [
                        [
                            'insert' => 'مرحباً بعودة الطلاب!'
                        ],
                        [
                            'attributes' => ['header' => 1],
                            'insert' => "\n"
                        ],
                        [
                            'insert' => 'نحن متحمسون لترحيب جميع الطلاب بالعام الدراسي الجديد. يرجى التحقق من جداولك الدراسية والتوجه إلى الفصول الدراسية المخصصة لك.'
                        ],
                        [
                            'insert' => "\n\n"
                        ],
                        [
                            'insert' => 'تذكيرات مهمة:'
                        ],
                        [
                            'attributes' => ['bold' => true],
                            'insert' => "\n"
                        ],
                        [
                            'insert' => '• تحقق من جدولك الدراسي\n• أحضر المواد المطلوبة\n• توجه إلى الفصل الدراسي بحلول الساعة 8:00 صباحاً'
                        ],
                        [
                            'insert' => "\n"
                        ]
                    ]
                ]),
                'publish_date' => $dates[array_rand($dates)],
                'photo' => null,
                'created_by' => 1,
            ],
            [
                'title' => 'مؤتمر الآباء والمعلمين',
                'content' => json_encode([
                    'ops' => [
                        [
                            'insert' => 'إعلان مؤتمر الآباء والمعلمين'
                        ],
                        [
                            'attributes' => ['header' => 2],
                            'insert' => "\n"
                        ],
                        [
                            'insert' => 'سيتم عقد مؤتمرات الآباء والمعلمين الأسبوع القادم. يرجى تحديد موعدك من خلال بوابة المدرسة.'
                        ],
                        [
                            'insert' => "\n\n"
                        ],
                        [
                            'insert' => 'تفاصيل المؤتمر:'
                        ],
                        [
                            'attributes' => ['bold' => true],
                            'insert' => "\n"
                        ],
                        [
                            'insert' => '• التاريخ: 15-16 مارس 2024\n• الوقت: 2:00 مساءً - 6:00 مساءً\n• المكان: الفصول الدراسية الفردية\n• المدة: 15 دقيقة لكل موعد'
                        ],
                        [
                            'insert' => "\n"
                        ]
                    ]
                ]),
                'publish_date' => $dates[array_rand($dates)],
                'photo' => null,
                'created_by' => 1,
            ],
            [
                'title' => 'إعلان معرض العلوم',
                'content' => json_encode([
                    'ops' => [
                        [
                            'insert' => 'معرض العلوم السنوي 2024'
                        ],
                        [
                            'attributes' => ['header' => 1],
                            'insert' => "\n"
                        ],
                        [
                            'insert' => 'سيتم عقد معرض العلوم في أسبوعين. يرجى إطلاع الطلاب على المشاريع التي يجب أن يبدأوا في تحضيرها.'
                        ],
                        [
                            'insert' => "\n\n"
                        ],
                        [
                            'insert' => 'فئات المشاريع:'
                        ],
                        [
                            'attributes' => ['bold' => true],
                            'insert' => "\n"
                        ],
                        [
                            'insert' => '1. الأحياء والعلوم الحية\n2. الكيمياء والعلوم الفيزيائية\n3. الهندسة والتكنولوجيا\n4. علوم البيئة'
                        ],
                        [
                            'insert' => "\n\n"
                        ],
                        [
                            'insert' => 'موعد التسليم: '
                        ],
                        [
                            'attributes' => ['bold' => true, 'underline' => true],
                            'insert' => '30 مارس 2024'
                        ],
                        [
                            'insert' => "\n"
                        ]
                    ]
                ]),
                'publish_date' => $dates[array_rand($dates)],
                'photo' => null,
                'created_by' => 1,
            ],
            [
                'title' => 'حدث الرياضة',
                'content' => json_encode([
                    'ops' => [
                        [
                            'insert' => 'حدث الرياضة السنوي 2024'
                        ],
                        [
                            'attributes' => ['header' => 1],
                            'insert' => "\n"
                        ],
                        [
                            'insert' => 'سيتم عقد حدث الرياضة السنوي في الشهر القادم. إشعارات التسجيل متوفرة في المكتب الرئيسي.'
                        ],
                        [
                            'insert' => "\n\n"
                        ],
                        [
                            'insert' => 'جدول الحدث:'
                        ],
                        [
                            'attributes' => ['bold' => true],
                            'insert' => "\n"
                        ],
                        [
                            'insert' => '• الأحداث الرياضية: 9:00 صباحاً - 12:00 ظهراً\n• الأحداث الميدانية: 1:00 ظهراً - 4:00 عصراً\n• الرياضات الفردية: 10:00 صباحاً - 3:00 عصراً'
                        ],
                        [
                            'insert' => "\n\n"
                        ],
                        [
                            'insert' => 'يرجى تشجيع جميع الطلاب على المشاركة!'
                        ],
                        [
                            'attributes' => ['italic' => true],
                            'insert' => "\n"
                        ]
                    ]
                ]),
                'publish_date' => $dates[array_rand($dates)],
                'photo' => null,
                'created_by' => 1,
            ],
            [
                'title' => 'جدول امتحان النصف الأول',
                'content' => json_encode([
                    'ops' => [
                        [
                            'insert' => 'جدول امتحان النصف الأول'
                        ],
                        [
                            'attributes' => ['header' => 2],
                            'insert' => "\n"
                        ],
                        [
                            'insert' => 'سيبدأ امتحانات النصف الأول في الأسبوع القادم. يرجى مراجعة جدول الامتحانات المعلق على اللوحة الإعلانية.'
                        ],
                        [
                            'insert' => "\n\n"
                        ],
                        [
                            'insert' => 'معلومات حول الامتحان:'
                        ],
                        [
                            'attributes' => ['bold' => true],
                            'insert' => "\n"
                        ],
                        [
                            'insert' => '• فترة الامتحان: 1-5 أبريل 2024\n• المدة: 2 ساعة لكل مادة\n• الوقت البدء: 9:00 صباحاً\n• المكان: صالة الامتحانات الرئيسية'
                        ],
                        [
                            'insert' => "\n\n"
                        ],
                        [
                            'insert' => 'ملاحظة: '
                        ],
                        [
                            'attributes' => ['bold' => true],
                            'insert' => 'يجب أن يصل الطلاب 30 دقيقة قبل وقت الامتحان'
                        ],
                        [
                            'insert' => "\n"
                        ]
                    ]
                ]),
                'publish_date' => $dates[array_rand($dates)],
                'photo' => null,
                'created_by' => 1,
            ],
        ];

        foreach ($news as $newsItem) {
            for ($i = 0; $i < 10; $i++) {

                $createdNews = News::create($newsItem);
                // Create news targets for demonstration
                $this->createNewsTargets($createdNews->id);
            }
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

            case 2:
                NewsTarget::create([
                    'news_id' => $newsId,
                    'grade_id' => 1, // Changed from 5 to 1
                    'section_id' => null,
                    'created_by' => 1,
                ]);
                break;

            case 3:
                $section = Section::where('grade_id', 1)->where('title', 'الأولى')->first(); // Changed from grade_id 3 to 1 and title 'A' to 'الأولى'
                if ($section) {
                    NewsTarget::create([
                        'news_id' => $newsId,
                        'grade_id' => null,
                        'section_id' => $section->id,
                        'created_by' => 1,
                    ]);
                }
                break;

            default:

                NewsTarget::create([
                    'news_id' => $newsId,
                    'grade_id' => null,
                    'section_id' => null,
                    'created_by' => 1,
                ]);

                break;
        }
    }
}
