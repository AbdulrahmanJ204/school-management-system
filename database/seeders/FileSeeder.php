<?php

namespace Database\Seeders;

use App\Enums\StringsManager\Files\FileStr;
use App\Enums\UserType;
use App\Models\File;
use App\Models\FileTarget;
use App\Models\Grade;
use App\Models\Section;
use App\Models\Subject;
use App\Models\User;
use App\Models\Year;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileSeeder extends Seeder
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
            $firstYearStartDate->copy()->addDays(5),
            $secondYearStartDate->copy()->addDays(5),
            $thirdYearStartDate->copy()->addDays(5),
            $firstYearEndDate->copy()->subDays(5),
            $secondYearEndDate->copy()->subDays(5),
            $thirdYearEndDate->copy()->subDays(5),
        ];

        // Get all necessary data
        $subjects = Subject::all();
        $grades = Grade::all();
        $sections = Section::all();
        $users = User::all()
            ->whereIn('user_type', [UserType::Admin->value, UserType::Teacher->value]);

        // Sample file data
        $fileTemplates = [
            [
                'title' => 'واجب الرياضيات الأول',
                'description' => 'مسائل أساسية في الجبر والهندسة للتدريب',
                'type' => 'public',
                'content' => "واجب الرياضيات\n\n1. حل المعادلة: 2س + 5 = 15\n2. أوجد مساحة المثلث الذي قاعدته 10 سم وارتفاعه 8 سم\n3. احسب محيط المستطيل الذي طوله 12 سم وعرضه 8 سم",
                'extension' => 'txt'
            ],
            [
                'title' => 'قالب تقرير المختبر العلمي',
                'description' => 'قالب لكتابة تقارير المختبر',
                'type' => 'helper',
                'content' => "قالب تقرير المختبر\n\nالهدف:\nالمواد:\nالإجراءات:\nالنتائج:\nالاستنتاج:",
                'extension' => 'txt'
            ],
            [
                'title' => 'إرشادات كتابة المقال',
                'description' => 'إرشادات لكتابة المقالات',
                'type' => 'helper',
                'content' => "إرشادات كتابة المقال\n\n1. مقدمة مع بيان الأطروحة\n2. فقرات الجسم مع الأدلة الداعمة\n3. خاتمة تلخص النقاط الرئيسية\n\nتذكر أن تستشهد بمصادرك!",
                'extension' => 'txt'
            ],
            [
                'title' => 'مشروع الجدول الزمني التاريخي',
                'description' => 'واجب الجدول الزمني للحرب العالمية الثانية',
                'type' => 'public',
                'content' => "مشروع الجدول الزمني التاريخي\n\nأنشئ جدولاً زمنياً للأحداث الرئيسية خلال الحرب العالمية الثانية (1939-1945)\n\nتضمن:\n- المعارك الرئيسية\n- التغييرات السياسية\n- الشخصيات الرئيسية\n- التطورات التكنولوجية",
                'extension' => 'txt'
            ],
            [
                'title' => 'ورقة الصيغ الكيميائية',
                'description' => 'الصيغ الكيميائية المهمة والثوابت',
                'type' => 'helper',
                'content' => "ورقة مرجعية في الكيمياء\n\nالصيغ الشائعة:\n- PV = nRT (قانون الغاز المثالي)\n- C = n/V (التركيز)\n- pH = -log[H+]\n\nالثوابت:\n- عدد أفوجادرو: 6.022 × 10²³\n- ثابت الغاز R: 8.314 J/(mol·K)",
                'extension' => 'txt'
            ],
            [
                'title' => 'مجموعة مسائل الفيزياء 3',
                'description' => 'مسائل الحركة والقوى',
                'type' => 'public',
                'content' => "مسائل الفيزياء - الحركة والقوى\n\n1. تسارع سيارة من السكون بمعدل 3 م/ث² لمدة 10 ثوانٍ. ما سرعتها النهائية؟\n2. احسب القوة المطلوبة لتسريع جسم كتلته 50 كجم بمعدل 2 م/ث²\n3. أُلقيت كرة لأعلى بسرعة ابتدائية 20 م/ث. إلى أي ارتفاع تصل؟",
                'extension' => 'txt'
            ],
        ];
        $storageDisk = 'public';
        for ($i = 0; $i < 4; $i++) {

            foreach ($fileTemplates as $index => $template) {
                // Determine subject (can be null for general files)
                $subject = rand(0, 10) > 2 ? $subjects->random() : null; // 80% chance to have subject
                $subjectCode = $subject ? $subject->code : FileStr::GeneralPath->value;

                // Create filename following your naming convention
                $hashedName = Str::random(40); // Simulate Laravel's hashName
                $filename = $subjectCode . FileStr::Separator->value . $hashedName . '.' . $template['extension'];

                // Create directory structure like your system
                $directoryPath = FileStr::LibraryPath->value . '/' . $subjectCode;
                $filePath = $directoryPath . '/' . $filename;

                // Create directory and store file
                Storage::disk($storageDisk)->makeDirectory($directoryPath);
                Storage::disk($storageDisk)->put($filePath, $template['content']);

                // Get file size
                $fileSize = Storage::disk($storageDisk)->size($filePath);

                // Create file record
                $file = File::create([
                    'subject_id' => $subject?->id,
                    'title' => $template['title'],
                    'description' => $template['description'],
                    'type' => $template['type'],
                    'file' => $filePath,
                    'size' => $fileSize,
                    'publish_date' => $dates[array_rand($dates)],
                    'created_by' => $users->random()->id,
                ]);

                // Create file targets with different scenarios
                $targetScenario = rand(1, 4);

                switch ($targetScenario) {
                    case 1: // General target (both null) - 25% chance
                        FileTarget::create([
                            'section_id' => null,
                            'grade_id' => null,
                            'file_id' => $file->id,
                            'created_by' => $users->random()->id,
                        ]);
                        break;

                    case 2: // Grade specific - 25% chance
                        FileTarget::create([
                            'section_id' => null,
                            'grade_id' => $grades->random()->id,
                            'file_id' => $file->id,
                            'created_by' => $users->random()->id,
                        ]);
                        break;

                    case 3: // Section specific - 25% chance
                        FileTarget::create([
                            'section_id' => $sections->random()->id,
                            'grade_id' => null,
                            'file_id' => $file->id,
                            'created_by' => $users->random()->id,
                        ]);
                        break;

                    case 4: // Multiple targets - 25% chance
                        $targetCount = rand(2, 3);
                        $isGradeTargets = rand(0, 1); // Decide if ALL targets are grade or section based

                        for ($i = 0; $i < $targetCount; $i++) {
                            if ($isGradeTargets) {
                                // All targets are grade-based
                                FileTarget::create([
                                    'section_id' => null,
                                    'grade_id' => $grades->random()->id,
                                    'file_id' => $file->id,
                                    'created_by' => $users->random()->id,
                                ]);
                            } else {
                                // All targets are section-based
                                FileTarget::create([
                                    'section_id' => $sections->random()->id,
                                    'grade_id' => null,
                                    'file_id' => $file->id,
                                    'created_by' => $users->random()->id,
                                ]);
                            }
                        }
                        break;
                }

                $this->command->info("Created file: {$template['title']} (Subject: " . ($subject ? $subject->name : 'General') . ")");
            }
        }
        // Create some PDF-like files (if you want to simulate different file types)
        $pdfTemplates = [
            'Complete Study Guide - Chapter 5',
            'Final Exam Review Materials',
            'Project Rubric and Guidelines',
        ];

        for ($i = 0; $i < 4; $i++) {
            foreach ($pdfTemplates as $index => $title) {
                // Some files can be general (no subject)
                $subject = rand(0, 10) > 3 ? $subjects->random() : null; // 70% chance to have subject
                $subjectCode = $subject ? $subject->code : FileStr::GeneralPath->value;

                // Create filename following your naming convention
                $hashedName = Str::random(40);
                $filename = $subjectCode . FileStr::Separator->value . $hashedName . '.txt'; // Using .txt for simplicity

                $directoryPath = FileStr::LibraryPath->value . '/' . $subjectCode;
                $filePath = $directoryPath . '/' . $filename;

                $content = "PDF Document: {$title}\n\nThis is a sample document that would normally be a PDF file.\nIt contains important information for students.\n\n" . str_repeat("Sample content line.\n", 50);

                Storage::disk($storageDisk)->makeDirectory($directoryPath);
                Storage::disk($storageDisk)->put($filePath, $content);

                $file = File::create([
                    'subject_id' => $subject?->id,
                    'title' => $title,
                    'description' => 'Important study material for students',
                    'type' => rand(0, 1) ? 'public' : 'helper',
                    'file' => $filePath,
                    'size' => Storage::disk($storageDisk)->size($filePath),
                    'publish_date' => $dates[array_rand($dates)],
                    'created_by' => $users->random()->id,
                ]);

                // Create different target scenarios
                $targetScenario = rand(1, 3);

                if ($targetScenario === 1) {
                    // General target (both null)
                    FileTarget::create([
                        'section_id' => null,
                        'grade_id' => null,
                        'file_id' => $file->id,
                        'created_by' => $users->random()->id,
                    ]);
                } else {
                    // Specific targets
                    $targetCount = rand(1, 2);
                    $isGradeTargets = rand(0, 1); // Decide if ALL targets are grade or section based

                    for ($i = 0; $i < $targetCount; $i++) {
                        if ($isGradeTargets) {
                            // All targets are grade-based
                            FileTarget::create([
                                'section_id' => null,
                                'grade_id' => $grades->random()->id,
                                'file_id' => $file->id,
                                'created_by' => $users->random()->id,
                            ]);
                        } else {
                            // All targets are section-based
                            FileTarget::create([
                                'section_id' => $sections->random()->id,
                                'grade_id' => null,
                                'file_id' => $file->id,
                                'created_by' => $users->random()->id,
                            ]);
                        }
                    }
                }

                $this->command->info("Created PDF file: {$title} (Subject: " . ($subject ? $subject->name : 'General') . ")");
            }
        }
        $this->command->info('File seeding completed successfully!');
    }
}
