# Quiz Seeder Documentation

## Overview
The QuizSeeder has been completely rewritten to create comprehensive quizzes for all sections and teachers in the system. It generates Arabic-language quizzes with appropriate questions based on subject types.

## Features

### 1. **Comprehensive Coverage**
- Creates quizzes for **ALL sections** in the system
- Assigns quizzes to **ALL teachers** (distributed evenly)
- Covers **ALL subjects** available in the system
- Targets **ALL grades** and **semesters**

### 2. **Arabic Language Content**
- Quiz names are in Arabic (e.g., "اختبار الرياضيات النصفي", "اختبار العلوم الشهري")
- Questions are in Arabic with subject-specific content
- Choices are in Arabic (أولاً، ثانياً، ثالثاً، رابعاً)
- Hints are in Arabic ("تلميح: راجع الدرس بعناية")

### 3. **Subject-Specific Questions**
The seeder automatically detects subject types and generates appropriate questions:

- **Mathematics (رياضيات)**: Arithmetic operations, geometry, numbers
- **Science (علوم)**: Planets, chemical elements, animals, plants
- **Arabic Language (عربية)**: Grammar, vocabulary, spelling
- **English Language (إنجليزية)**: Grammar, vocabulary, spelling
- **Social Studies (اجتماعية)**: Geography, history, Islamic studies

### 4. **Smart Distribution**
- Teachers are assigned quizzes using round-robin distribution
- Each section-subject combination gets a unique quiz
- Questions are randomly generated but maintain subject relevance
- Quiz scores range from 50-100 points

## How It Works

### 1. **Data Collection**
```php
$teachers = Teacher::all();
$sections = Section::all();
$subjects = Subject::all();
$semesters = Semester::all();
```

### 2. **Quiz Creation Loop**
```php
foreach ($sections as $section) {
    foreach ($subjects as $subject) {
        // Create quiz for each section-subject combination
        // Assign to teacher using round-robin
        // Generate subject-specific questions
    }
}
```

### 3. **Question Generation**
- Each quiz gets 5-10 questions
- Questions are generated based on subject type
- Choices are appropriate for the subject
- Right answers are randomly assigned

### 4. **Target Assignment**
Each quiz is properly targeted to:
- Specific grade
- Specific subject
- Specific section
- Appropriate semester

## Usage

### Running the Seeder
```bash
php artisan db:seed --class=QuizSeeder
```

### Expected Output
```
Created 108 quizzes for all sections and teachers.
```

### Verification
```bash
# Check total quizzes created
php artisan tinker --execute="echo 'Total quizzes: ' . App\Models\Quiz::count();"

# Check total questions created
php artisan tinker --execute="echo 'Total questions: ' . App\Models\Question::count();"

# Check quiz distribution
php artisan tinker --execute="echo 'Total sections with quizzes: ' . App\Models\QuizTarget::distinct('section_id')->count('section_id');"
```

## Dependencies

The QuizSeeder requires these seeders to run first:
1. `TeacherSeeder` (or UserFactory for teachers)
2. `SectionSeeder`
3. `SubjectSeeder`
4. `SemesterSeeder`
5. `GradeSeeder`
6. `YearSeeder`

## Database Impact

- **Quizzes**: Creates one quiz per section-subject combination
- **Questions**: 5-10 questions per quiz
- **Quiz Targets**: One target per quiz linking to grade, subject, section, and semester
- **Total Records**: Approximately 100+ quizzes with 500+ questions

## Customization

### Adding New Subject Types
To add support for new subjects, modify the `getSubjectType()` method:

```php
private function getSubjectType($subjectName)
{
    $subjectName = strtolower($subjectName);
    
    if (str_contains($subjectName, 'فنون') || str_contains($subjectName, 'arts')) {
        return 'arts';
    }
    // ... existing types
}
```

### Adding New Question Templates
Add new question templates to the `$questionTemplates` array:

```php
'arts' => [
    'أي من الألوان التالية هو لون أساسي؟',
    'ما هو نوع الفن الذي يستخدم الألوان؟',
    // ... more questions
]
```

## Notes

- The seeder is idempotent and can be run multiple times
- Existing quizzes are not duplicated
- Questions are generated with realistic Arabic content
- All quizzes are properly linked to the curriculum structure
- The seeder handles edge cases (missing semesters, etc.)

## Troubleshooting

### Common Issues

1. **"Teachers not found"**: Run UserFactory or TeacherSeeder first
2. **"Sections not found"**: Run SectionSeeder first
3. **"Subjects not found"**: Run SubjectSeeder first
4. **"Semesters not found"**: Run SemesterSeeder first

### Performance
- The seeder processes all combinations in a single run
- For large datasets, consider running in chunks
- Monitor memory usage for very large systems
