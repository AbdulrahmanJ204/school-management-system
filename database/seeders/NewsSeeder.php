<?php

namespace Database\Seeders;

use App\Models\Grade;
use App\Models\News;
use App\Models\NewsTarget;
use App\Models\Section;
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
        // Create dates for news publishing (using current year as fallback)
        $currentYear = now()->year;
        $dates = [
            now()->setDate($currentYear, 9, 15), // September 15
            now()->setDate($currentYear, 10, 20), // October 20
            now()->setDate($currentYear, 11, 25), // November 25
            now()->setDate($currentYear, 12, 10), // December 10
            now()->setDate($currentYear, 1, 15),  // January 15
            now()->setDate($currentYear, 2, 20),  // February 20
        ];

        // Get all available grades and sections
        $grades = Grade::all();
        $sections = Section::all();

        $news = [
            // General News (for all students)
            [
                'title' => 'مرحباً بكم في العام الدراسي الجديد 2024-2025',
                'content' => json_encode([
                    'ops' => [
                        [
                            'insert' => 'مرحباً بجميع الطلاب وأولياء الأمور!'
                        ],
                        [
                            'attributes' => ['header' => 1],
                            'insert' => "\n"
                        ],
                        [
                            'insert' => 'نحن متحمسون لبدء عام دراسي جديد مليء بالفرص والإنجازات. نتمنى لجميع الطلاب عاماً دراسياً موفقاً ومليئاً بالتعلم والنمو.'
                        ],
                        [
                            'insert' => "\n\n"
                        ],
                        [
                            'insert' => 'تذكيرات مهمة للطلاب:'
                        ],
                        [
                            'attributes' => ['bold' => true],
                            'insert' => "\n"
                        ],
                        [
                            'insert' => '• تحقق من جدولك الدراسي\n• أحضر جميع المواد المطلوبة\n• توجه إلى الفصل الدراسي بحلول الساعة 7:45 صباحاً\n• احرص على الحضور المنتظم'
                        ],
                        [
                            'insert' => "\n\n"
                        ],
                        [
                            'insert' => 'نتمنى لكم عاماً دراسياً موفقاً!'
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
                'target_type' => 'general'
            ],
            [
                'title' => 'إعلان مؤتمر الآباء والمعلمين الفصلي',
                'content' => json_encode([
                    'ops' => [
                        [
                            'insert' => 'إعلان مؤتمر الآباء والمعلمين الفصلي'
                        ],
                        [
                            'attributes' => ['header' => 2],
                            'insert' => "\n"
                        ],
                        [
                            'insert' => 'سيتم عقد مؤتمرات الآباء والمعلمين الفصلية خلال الأسبوع القادم. هذه فرصة مهمة لمناقشة تقدم الطلاب والتواصل مع المعلمين.'
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
                            'insert' => '• التاريخ: 20-22 نوفمبر 2024\n• الوقت: 2:00 مساءً - 6:00 مساءً\n• المكان: الفصول الدراسية الفردية\n• المدة: 20 دقيقة لكل موعد'
                        ],
                        [
                            'insert' => "\n\n"
                        ],
                        [
                            'insert' => 'يرجى تحديد موعدك من خلال بوابة المدرسة أو الاتصال بالمكتب الرئيسي.'
                        ],
                        [
                            'insert' => "\n"
                        ]
                    ]
                ]),
                'publish_date' => $dates[array_rand($dates)],
                'photo' => null,
                'created_by' => 1,
                'target_type' => 'general'
            ],
            [
                'title' => 'معرض العلوم والتكنولوجيا السنوي',
                'content' => json_encode([
                    'ops' => [
                        [
                            'insert' => 'معرض العلوم والتكنولوجيا السنوي 2024'
                        ],
                        [
                            'attributes' => ['header' => 1],
                            'insert' => "\n"
                        ],
                        [
                            'insert' => 'سيتم عقد معرض العلوم والتكنولوجيا السنوي في نهاية الشهر القادم. هذا الحدث يعرض إبداعات الطلاب ومشاريعهم العلمية.'
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
                            'insert' => '1. الأحياء والعلوم الحية\n2. الكيمياء والعلوم الفيزيائية\n3. الهندسة والتكنولوجيا\n4. علوم البيئة والاستدامة\n5. الرياضيات التطبيقية'
                        ],
                        [
                            'insert' => "\n\n"
                        ],
                        [
                            'insert' => 'موعد التسليم: '
                        ],
                        [
                            'attributes' => ['bold' => true, 'underline' => true],
                            'insert' => '15 ديسمبر 2024'
                        ],
                        [
                            'insert' => "\n\n"
                        ],
                        [
                            'insert' => 'سيتم تقييم المشاريع من قبل لجنة من المعلمين والمتخصصين.'
                        ],
                        [
                            'insert' => "\n"
                        ]
                    ]
                ]),
                'publish_date' => $dates[array_rand($dates)],
                'photo' => null,
                'created_by' => 1,
                'target_type' => 'general'
            ],
            [
                'title' => 'اليوم الرياضي السنوي للمدرسة',
                'content' => json_encode([
                    'ops' => [
                        [
                            'insert' => 'اليوم الرياضي السنوي للمدرسة 2024'
                        ],
                        [
                            'attributes' => ['header' => 1],
                            'insert' => "\n"
                        ],
                        [
                            'insert' => 'سيتم عقد اليوم الرياضي السنوي في الشهر القادم. هذا الحدث يشجع على النشاط البدني والروح الرياضية.'
                        ],
                        [
                            'insert' => "\n\n"
                        ],
                        [
                            'insert' => 'جدول الأنشطة الرياضية:'
                        ],
                        [
                            'attributes' => ['bold' => true],
                            'insert' => "\n"
                        ],
                        [
                            'insert' => '• الألعاب الجماعية: 9:00 صباحاً - 12:00 ظهراً\n• المسابقات الفردية: 1:00 ظهراً - 4:00 عصراً\n• سباقات الجري: 10:00 صباحاً - 11:00 صباحاً\n• مسابقات القفز: 2:00 ظهراً - 3:00 عصراً'
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
                'target_type' => 'general'
            ],
            [
                'title' => 'جدول امتحانات الفصل الأول',
                'content' => json_encode([
                    'ops' => [
                        [
                            'insert' => 'جدول امتحانات الفصل الأول'
                        ],
                        [
                            'attributes' => ['header' => 2],
                            'insert' => "\n"
                        ],
                        [
                            'insert' => 'سيبدأ امتحانات الفصل الأول في الأسبوع القادم. يرجى مراجعة جدول الامتحانات المعلق على اللوحة الإعلانية.'
                        ],
                        [
                            'insert' => "\n\n"
                        ],
                        [
                            'insert' => 'معلومات مهمة حول الامتحانات:'
                        ],
                        [
                            'attributes' => ['bold' => true],
                            'insert' => "\n"
                        ],
                        [
                            'insert' => '• فترة الامتحان: 10-14 ديسمبر 2024\n• المدة: ساعة ونصف لكل مادة\n• وقت البدء: 9:00 صباحاً\n• المكان: صالة الامتحانات الرئيسية'
                        ],
                        [
                            'insert' => "\n\n"
                        ],
                        [
                            'insert' => 'ملاحظة مهمة: '
                        ],
                        [
                            'attributes' => ['bold' => true],
                            'insert' => 'يجب أن يصل الطلاب 30 دقيقة قبل وقت الامتحان'
                        ],
                        [
                            'insert' => "\n\n"
                        ],
                        [
                            'insert' => 'نتمنى لجميع الطلاب التوفيق في الامتحانات!'
                        ],
                        [
                            'insert' => "\n"
                        ]
                    ]
                ]),
                'publish_date' => $dates[array_rand($dates)],
                'photo' => null,
                'created_by' => 1,
                'target_type' => 'general'
            ],

            // Grade-specific news
            [
                'title' => 'أخبار خاصة بالصف الأول الابتدائي',
                'content' => json_encode([
                    'ops' => [
                        [
                            'insert' => 'أخبار خاصة بالصف الأول الابتدائي'
                        ],
                        [
                            'attributes' => ['header' => 1],
                            'insert' => "\n"
                        ],
                        [
                            'insert' => 'مرحباً بطلاب الصف الأول الابتدائي وأولياء أمورهم!'
                        ],
                        [
                            'insert' => "\n\n"
                        ],
                        [
                            'insert' => 'نحن سعداء بوجودكم معنا في بداية رحلة التعلم. هذا العام سيكون مليئاً بالمغامرات التعليمية الممتعة.'
                        ],
                        [
                            'insert' => "\n\n"
                        ],
                        [
                            'insert' => 'أنشطة خاصة بالصف الأول:'
                        ],
                        [
                            'attributes' => ['bold' => true],
                            'insert' => "\n"
                        ],
                        [
                            'insert' => '• رحلات تعليمية إلى المكتبة\n• أنشطة فنية وإبداعية\n• ألعاب تعليمية تفاعلية\n• حفلات نهاية الفصل الدراسي'
                        ],
                        [
                            'insert' => "\n\n"
                        ],
                        [
                            'insert' => 'نتمنى لكم عاماً دراسياً موفقاً ومليئاً بالمرح!'
                        ],
                        [
                            'insert' => "\n"
                        ]
                    ]
                ]),
                'publish_date' => $dates[array_rand($dates)],
                'photo' => null,
                'created_by' => 1,
                'target_type' => 'grade',
                'grade_id' => 1
            ],
            [
                'title' => 'أخبار خاصة بالصف الثاني الابتدائي',
                'content' => json_encode([
                    'ops' => [
                        [
                            'insert' => 'أخبار خاصة بالصف الثاني الابتدائي'
                        ],
                        [
                            'attributes' => ['header' => 1],
                            'insert' => "\n"
                        ],
                        [
                            'insert' => 'مرحباً بطلاب الصف الثاني الابتدائي!'
                        ],
                        [
                            'insert' => "\n\n"
                        ],
                        [
                            'insert' => 'أهلاً وسهلاً بكم في عامكم الثاني معنا. سنواصل رحلة التعلم مع تحديات جديدة وممتعة.'
                        ],
                        [
                            'insert' => "\n\n"
                        ],
                        [
                            'insert' => 'مشاريع خاصة بالصف الثاني:'
                        ],
                        [
                            'attributes' => ['bold' => true],
                            'insert' => "\n"
                        ],
                        [
                            'insert' => '• مشروع القراءة الأسبوعي\n• مشاريع العلوم البسيطة\n• معرض الفنون\n• مسابقة الرياضيات'
                        ],
                        [
                            'insert' => "\n\n"
                        ],
                        [
                            'insert' => 'نتمنى لكم عاماً مليئاً بالإنجازات!'
                        ],
                        [
                            'insert' => "\n"
                        ]
                    ]
                ]),
                'publish_date' => $dates[array_rand($dates)],
                'photo' => null,
                'created_by' => 1,
                'target_type' => 'grade',
                'grade_id' => 2
            ],
            [
                'title' => 'أخبار خاصة بالصف الثالث الابتدائي',
                'content' => json_encode([
                    'ops' => [
                        [
                            'insert' => 'أخبار خاصة بالصف الثالث الابتدائي'
                        ],
                        [
                            'attributes' => ['header' => 1],
                            'insert' => "\n"
                        ],
                        [
                            'insert' => 'مرحباً بطلاب الصف الثالث الابتدائي!'
                        ],
                        [
                            'insert' => "\n\n"
                        ],
                        [
                            'insert' => 'أهلاً وسهلاً بكم في عامكم الثالث. هذا العام سيكون مليئاً بالتحديات التعليمية المثيرة.'
                        ],
                        [
                            'insert' => "\n\n"
                        ],
                        [
                            'insert' => 'أنشطة خاصة بالصف الثالث:'
                        ],
                        [
                            'attributes' => ['bold' => true],
                            'insert' => "\n"
                        ],
                        [
                            'insert' => '• مشاريع البحث العلمي\n• مسابقات الكتابة الإبداعية\n• أنشطة الرياضيات المتقدمة\n• رحلات تعليمية خارجية'
                        ],
                        [
                            'insert' => "\n\n"
                        ],
                        [
                            'insert' => 'نتمنى لكم عاماً مليئاً بالتعلم والإنجاز!'
                        ],
                        [
                            'insert' => "\n"
                        ]
                    ]
                ]),
                'publish_date' => $dates[array_rand($dates)],
                'photo' => null,
                'created_by' => 1,
                'target_type' => 'grade',
                'grade_id' => 3
            ],

            // Section-specific news
            [
                'title' => 'أخبار خاصة بالشعبة الأولى',
                'content' => json_encode([
                    'ops' => [
                        [
                            'insert' => 'أخبار خاصة بالشعبة الأولى'
                        ],
                        [
                            'attributes' => ['header' => 1],
                            'insert' => "\n"
                        ],
                        [
                            'insert' => 'مرحباً بطلاب الشعبة الأولى في جميع الصفوف!'
                        ],
                        [
                            'insert' => "\n\n"
                        ],
                        [
                            'insert' => 'نحن فخورون بأدائكم المتميز وإنجازاتكم المستمرة. استمروا في العمل الجاد!'
                        ],
                        [
                            'insert' => "\n\n"
                        ],
                        [
                            'insert' => 'أنشطة خاصة بالشعبة الأولى:'
                        ],
                        [
                            'attributes' => ['bold' => true],
                            'insert' => "\n"
                        ],
                        [
                            'insert' => '• مسابقة القراءة الأسبوعية\n• معرض العلوم الخاص\n• أنشطة الرياضيات التفاعلية\n• حفلات التكريم'
                        ],
                        [
                            'insert' => "\n\n"
                        ],
                        [
                            'insert' => 'نتمنى لكم المزيد من التميز!'
                        ],
                        [
                            'insert' => "\n"
                        ]
                    ]
                ]),
                'publish_date' => $dates[array_rand($dates)],
                'photo' => null,
                'created_by' => 1,
                'target_type' => 'section',
                'section_id' => 1
            ],
            [
                'title' => 'أخبار خاصة بالشعبة الثانية',
                'content' => json_encode([
                    'ops' => [
                        [
                            'insert' => 'أخبار خاصة بالشعبة الثانية'
                        ],
                        [
                            'attributes' => ['header' => 1],
                            'insert' => "\n"
                        ],
                        [
                            'insert' => 'مرحباً بطلاب الشعبة الثانية!'
                        ],
                        [
                            'insert' => "\n\n"
                        ],
                        [
                            'insert' => 'نحن سعداء بتقدمكم المستمر وإبداعكم في جميع المجالات. استمروا في التميز!'
                        ],
                        [
                            'insert' => "\n\n"
                        ],
                        [
                            'insert' => 'أنشطة خاصة بالشعبة الثانية:'
                        ],
                        [
                            'attributes' => ['bold' => true],
                            'insert' => "\n"
                        ],
                        [
                            'insert' => '• مسابقة الكتابة الإبداعية\n• معرض الفنون\n• أنشطة العلوم التجريبية\n• مسابقات الرياضيات'
                        ],
                        [
                            'insert' => "\n\n"
                        ],
                        [
                            'insert' => 'نتمنى لكم المزيد من الإنجازات!'
                        ],
                        [
                            'insert' => "\n"
                        ]
                    ]
                ]),
                'publish_date' => $dates[array_rand($dates)],
                'photo' => null,
                'created_by' => 1,
                'target_type' => 'section',
                'section_id' => 2
            ],
            [
                'title' => 'أخبار خاصة بالشعبة الثالثة',
                'content' => json_encode([
                    'ops' => [
                        [
                            'insert' => 'أخبار خاصة بالشعبة الثالثة'
                        ],
                        [
                            'attributes' => ['header' => 1],
                            'insert' => "\n"
                        ],
                        [
                            'insert' => 'مرحباً بطلاب الشعبة الثالثة!'
                        ],
                        [
                            'insert' => "\n\n"
                        ],
                        [
                            'insert' => 'نحن فخورون بأدائكم المتميز وتفاعلكم الإيجابي في جميع الأنشطة. استمروا في التقدم!'
                        ],
                        [
                            'insert' => "\n\n"
                        ],
                        [
                            'insert' => 'أنشطة خاصة بالشعبة الثالثة:'
                        ],
                        [
                            'attributes' => ['bold' => true],
                            'insert' => "\n"
                        ],
                        [
                            'insert' => '• مشاريع العلوم المتقدمة\n• معرض التكنولوجيا\n• أنشطة الرياضيات المتقدمة\n• مسابقات القراءة'
                        ],
                        [
                            'insert' => "\n\n"
                        ],
                        [
                            'insert' => 'نتمنى لكم المزيد من التميز!'
                        ],
                        [
                            'insert' => "\n"
                        ]
                    ]
                ]),
                'publish_date' => $dates[array_rand($dates)],
                'photo' => null,
                'created_by' => 1,
                'target_type' => 'section',
                'section_id' => 3
            ],

            // Additional general news
            [
                'title' => 'برنامج التوعية الصحية',
                'content' => json_encode([
                    'ops' => [
                        [
                            'insert' => 'برنامج التوعية الصحية المدرسي'
                        ],
                        [
                            'attributes' => ['header' => 1],
                            'insert' => "\n"
                        ],
                        [
                            'insert' => 'سيتم إطلاق برنامج التوعية الصحية المدرسي هذا الأسبوع. يهدف البرنامج إلى تعزيز الوعي الصحي لدى الطلاب.'
                        ],
                        [
                            'insert' => "\n\n"
                        ],
                        [
                            'insert' => 'مواضيع البرنامج:'
                        ],
                        [
                            'attributes' => ['bold' => true],
                            'insert' => "\n"
                        ],
                        [
                            'insert' => '• النظافة الشخصية\n• التغذية الصحية\n• النشاط البدني\n• الصحة النفسية\n• الوقاية من الأمراض'
                        ],
                        [
                            'insert' => "\n\n"
                        ],
                        [
                            'insert' => 'سيتم تنفيذ البرنامج من خلال محاضرات تفاعلية وأنشطة عملية.'
                        ],
                        [
                            'insert' => "\n"
                        ]
                    ]
                ]),
                'publish_date' => $dates[array_rand($dates)],
                'photo' => null,
                'created_by' => 1,
                'target_type' => 'general'
            ],
            [
                'title' => 'مشروع القراءة الصيفية',
                'content' => json_encode([
                    'ops' => [
                        [
                            'insert' => 'مشروع القراءة الصيفية 2024'
                        ],
                        [
                            'attributes' => ['header' => 1],
                            'insert' => "\n"
                        ],
                        [
                            'insert' => 'نحن متحمسون لإطلاق مشروع القراءة الصيفية لهذا العام. يهدف المشروع إلى تشجيع الطلاب على القراءة خلال العطلة الصيفية.'
                        ],
                        [
                            'insert' => "\n\n"
                        ],
                        [
                            'insert' => 'تفاصيل المشروع:'
                        ],
                        [
                            'attributes' => ['bold' => true],
                            'insert' => "\n"
                        ],
                        [
                            'insert' => '• قائمة الكتب الموصى بها لكل صف\n• جوائز للقراء المتميزين\n• أنشطة مناقشة الكتب\n• معرض للكتب المقروءة'
                        ],
                        [
                            'insert' => "\n\n"
                        ],
                        [
                            'insert' => 'سيتم توزيع قوائم الكتب في نهاية العام الدراسي.'
                        ],
                        [
                            'insert' => "\n"
                        ]
                    ]
                ]),
                'publish_date' => $dates[array_rand($dates)],
                'photo' => null,
                'created_by' => 1,
                'target_type' => 'general'
            ]
        ];

        foreach ($news as $newsItem) {
            $createdNews = News::create([
                'title' => $newsItem['title'],
                'content' => $newsItem['content'],
                'publish_date' => $newsItem['publish_date'],
                'photo' => $newsItem['photo'],
                'created_by' => $newsItem['created_by'],
            ]);

            // Create news targets based on type
            $this->createNewsTargets($createdNews->id, $newsItem['target_type'], $newsItem['grade_id'] ?? null, $newsItem['section_id'] ?? null);
        }

        // Create additional random news for variety
        $this->createRandomNews($grades, $sections, $dates);
    }

    private function createNewsTargets($newsId, $targetType, $gradeId = null, $sectionId = null)
    {
        switch ($targetType) {
            case 'general':
                // Target all students (no specific grade or section)
                NewsTarget::create([
                    'news_id' => $newsId,
                    'grade_id' => null,
                    'section_id' => null,
                    'created_by' => 1,
                ]);
                break;

            case 'grade':
                // Target specific grade
                NewsTarget::create([
                    'news_id' => $newsId,
                    'grade_id' => $gradeId,
                    'section_id' => null,
                    'created_by' => 1,
                ]);
                break;

            case 'section':
                // Target specific section
                NewsTarget::create([
                    'news_id' => $newsId,
                    'grade_id' => null,
                    'section_id' => $sectionId,
                    'created_by' => 1,
                ]);
                break;
        }
    }

    private function createRandomNews($grades, $sections, $dates)
    {
        $randomNewsTitles = [
            'إعلان مهم للطلاب',
            'أخبار المدرسة الأسبوعية',
            'تحديثات المدرسة',
            'إعلانات جديدة',
            'أخبار الطلاب',
            'تحديثات مهمة',
            'إعلانات المدرسة',
            'أخبار الأسبوع'
        ];

        $randomNewsContent = [
            'هذا إعلان مهم لجميع الطلاب. يرجى الانتباه لهذه المعلومات.',
            'أخبار أسبوعية جديدة من المدرسة. نتمنى لكم أسبوعاً موفقاً.',
            'تحديثات مهمة من إدارة المدرسة. يرجى متابعة هذه الأخبار.',
            'إعلانات جديدة للطلاب. نرجو الانتباه لهذه المعلومات.',
            'أخبار الطلاب لهذا الأسبوع. نتمنى لكم التوفيق.',
            'تحديثات مهمة من المدرسة. يرجى متابعة هذه الأخبار.',
            'إعلانات المدرسة الجديدة. نرجو الانتباه لهذه المعلومات.',
            'أخبار الأسبوع من المدرسة. نتمنى لكم أسبوعاً مليئاً بالنجاح.'
        ];

        // Create random news for each grade
        foreach ($grades as $grade) {
            $news = News::create([
                'title' => $randomNewsTitles[array_rand($randomNewsTitles)] . ' - ' . $grade->title,
                'content' => json_encode([
                    'ops' => [
                        [
                            'insert' => $randomNewsContent[array_rand($randomNewsContent)]
                        ],
                        [
                            'insert' => "\n\n"
                        ],
                        [
                            'insert' => 'هذا الإعلان خاص بطلاب ' . $grade->title
                        ],
                        [
                            'insert' => "\n"
                        ]
                    ]
                ]),
                'publish_date' => $dates[array_rand($dates)],
                'photo' => null,
                'created_by' => 1,
            ]);

            NewsTarget::create([
                'news_id' => $news->id,
                'grade_id' => $grade->id,
                'section_id' => null,
                'created_by' => 1,
            ]);
        }

        // Create random news for each section
        foreach ($sections as $section) {
            $news = News::create([
                'title' => $randomNewsTitles[array_rand($randomNewsTitles)] . ' - ' . $section->title,
                'content' => json_encode([
                    'ops' => [
                        [
                            'insert' => $randomNewsContent[array_rand($randomNewsContent)]
                        ],
                        [
                            'insert' => "\n\n"
                        ],
                        [
                            'insert' => 'هذا الإعلان خاص بطلاب الشعبة ' . $section->title
                        ],
                        [
                            'insert' => "\n"
                        ]
                    ]
                ]),
                'publish_date' => $dates[array_rand($dates)],
                'photo' => null,
                'created_by' => 1,
            ]);

            NewsTarget::create([
                'news_id' => $news->id,
                'grade_id' => null,
                'section_id' => $section->id,
                'created_by' => 1,
            ]);
        }
    }
}
