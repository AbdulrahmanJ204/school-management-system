<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeacherSectionSubject;
use App\Models\User;
use App\Models\Semester;
use App\Models\Section;
use App\Models\Grade;
use App\Models\MainSubject;
use App\Models\Year;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TeacherStudentMarksApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test data
        $this->createTestData();
    }

    private function createTestData(): void
    {
        // Create year
        $year = Year::factory()->create(['is_active' => true]);
        
        // Create semester
        $semester = Semester::factory()->create([
            'year_id' => $year->id,
            'is_active' => true
        ]);
        
        // Create grade
        $grade = Grade::factory()->create();
        
        // Create main subject
        $mainSubject = MainSubject::factory()->create([
            'grade_id' => $grade->id
        ]);
        
        // Create section
        $section = Section::factory()->create([
            'grade_id' => $grade->id
        ]);
        
        // Create subject
        $subject = Subject::factory()->create([
            'main_subject_id' => $mainSubject->id,
            'homework_percentage' => 20,
            'oral_percentage' => 10,
            'activity_percentage' => 15,
            'quiz_percentage' => 25,
            'exam_percentage' => 30,
            'full_mark' => 100
        ]);
        
        // Create teacher user
        $teacherUser = User::factory()->create(['user_type' => 'teacher']);
        $teacher = Teacher::factory()->create(['user_id' => $teacherUser->id]);
        
        // Create student user
        $studentUser = User::factory()->create(['user_type' => 'student']);
        $student = Student::factory()->create(['user_id' => $studentUser->id]);
        
        // Create student enrollment
        $enrollment = StudentEnrollment::factory()->create([
            'student_id' => $student->id,
            'section_id' => $section->id,
            'semester_id' => $semester->id,
            'year_id' => $year->id
        ]);
        
        // Create teacher section subject assignment
        TeacherSectionSubject::factory()->create([
            'teacher_id' => $teacher->id,
            'subject_id' => $subject->id,
            'section_id' => $section->id,
            'grade_id' => $grade->id,
            'is_active' => true
        ]);
        
        // Store references for testing
        $this->teacherUser = $teacherUser;
        $this->student = $student;
        $this->subject = $subject;
        $this->enrollment = $enrollment;
    }

    public function test_teacher_can_add_student_marks()
    {
        Sanctum::actingAs($this->teacherUser);

        $markData = [
            'subject_id' => $this->subject->id,
            'semester_id' => $this->enrollment->semester_id,
            'section_id' => $this->enrollment->section_id,
            'homework' => 85,
            'oral' => 90,
            'activity' => 88,
            'quiz' => 92,
            'exam' => 87
        ];

        $response = $this->postJson("/api/teacher/students/{$this->student->id}/marks", $markData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'successful',
                    'message',
                    'data' => [
                        'student_id',
                        'subject_id',
                        'homework',
                        'oral',
                        'activity',
                        'quiz',
                        'exam',
                        'total',
                        'enrollment_id',
                        'id'
                    ],
                    'status_code'
                ]);

        $this->assertDatabaseHas('student_marks', [
            'enrollment_id' => $this->enrollment->id,
            'subject_id' => $this->subject->id,
            'homework' => 85,
            'oral' => 90,
            'activity' => 88,
            'quiz' => 92,
            'exam' => 87
        ]);
    }

    public function test_teacher_can_update_existing_student_marks()
    {
        Sanctum::actingAs($this->teacherUser);

        // First, create marks
        $initialMarks = [
            'subject_id' => $this->subject->id,
            'semester_id' => $this->enrollment->semester_id,
            'section_id' => $this->enrollment->section_id,
            'homework' => 80,
            'oral' => 85,
            'activity' => 82,
            'quiz' => 88,
            'exam' => 85
        ];

        $this->postJson("/api/teacher/students/{$this->student->id}/marks", $initialMarks);

        // Then update them
        $updatedMarks = [
            'subject_id' => $this->subject->id,
            'semester_id' => $this->enrollment->semester_id,
            'section_id' => $this->enrollment->section_id,
            'homework' => 90,
            'oral' => 95,
            'activity' => 92,
            'quiz' => 98,
            'exam' => 95
        ];

        $response = $this->postJson("/api/teacher/students/{$this->student->id}/marks", $updatedMarks);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'successful',
                    'message',
                    'data' => [
                        'student_id',
                        'subject_id',
                        'homework',
                        'oral',
                        'activity',
                        'quiz',
                        'exam',
                        'total',
                        'enrollment_id',
                        'id'
                    ],
                    'status_code'
                ]);

        $this->assertDatabaseHas('student_marks', [
            'enrollment_id' => $this->enrollment->id,
            'subject_id' => $this->subject->id,
            'homework' => 90,
            'oral' => 95,
            'activity' => 92,
            'quiz' => 98,
            'exam' => 95
        ]);
    }

    public function test_teacher_cannot_add_marks_for_unauthorized_subject()
    {
        Sanctum::actingAs($this->teacherUser);

        // Create another subject that the teacher is not assigned to
        $unauthorizedSubject = Subject::factory()->create([
            'main_subject_id' => $this->subject->main_subject_id,
            'homework_percentage' => 20,
            'oral_percentage' => 10,
            'activity_percentage' => 15,
            'quiz_percentage' => 25,
            'exam_percentage' => 30,
            'full_mark' => 100
        ]);

        $markData = [
            'subject_id' => $unauthorizedSubject->id,
            'semester_id' => $this->enrollment->semester_id,
            'section_id' => $this->enrollment->section_id,
            'homework' => 85,
            'oral' => 90,
            'activity' => 88,
            'quiz' => 92,
            'exam' => 87
        ];

        $response = $this->postJson("/api/teacher/students/{$this->student->id}/marks", $markData);

        $response->assertStatus(403)
                ->assertJson([
                    'successful' => false,
                    'message' => 'غير مصرح لك بتدريس هذه المادة'
                ]);
    }

    public function test_teacher_cannot_add_marks_for_nonexistent_student()
    {
        Sanctum::actingAs($this->teacherUser);

        $markData = [
            'subject_id' => $this->subject->id,
            'semester_id' => $this->enrollment->semester_id,
            'section_id' => $this->enrollment->section_id,
            'homework' => 85,
            'oral' => 90,
            'activity' => 88,
            'quiz' => 92,
            'exam' => 87
        ];

        $response = $this->postJson("/api/teacher/students/99999/marks", $markData);

        $response->assertStatus(404)
                ->assertJson([
                    'successful' => false,
                    'message' => 'الطالب غير موجود'
                ]);
    }

    public function test_validation_requires_at_least_one_mark()
    {
        Sanctum::actingAs($this->teacherUser);

        $markData = [
            'subject_id' => $this->subject->id,
            'semester_id' => $this->enrollment->semester_id,
            'section_id' => $this->enrollment->section_id
            // No marks provided
        ];

        $response = $this->postJson("/api/teacher/students/{$this->student->id}/marks", $markData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['marks']);
    }

    public function test_validation_requires_valid_subject_id()
    {
        Sanctum::actingAs($this->teacherUser);

        $markData = [
            'subject_id' => 99999, // Non-existent subject
            'semester_id' => $this->enrollment->semester_id,
            'section_id' => $this->enrollment->section_id,
            'homework' => 85
        ];

        $response = $this->postJson("/api/teacher/students/{$this->student->id}/marks", $markData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['subject_id']);
    }

    public function test_marks_cannot_exceed_subject_full_mark()
    {
        Sanctum::actingAs($this->teacherUser);

        $markData = [
            'subject_id' => $this->subject->id,
            'semester_id' => $this->enrollment->semester_id,
            'section_id' => $this->enrollment->section_id,
            'homework' => 150, // Exceeds full_mark of 100
            'oral' => 90,
            'activity' => 88,
            'quiz' => 92,
            'exam' => 87
        ];

        $response = $this->postJson("/api/teacher/students/{$this->student->id}/marks", $markData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['homework']);
    }

    public function test_non_teacher_user_cannot_access_api()
    {
        // Create a student user and try to access the API
        $studentUser = User::factory()->create(['user_type' => 'student']);
        Sanctum::actingAs($studentUser);

        $markData = [
            'subject_id' => $this->subject->id,
            'semester_id' => $this->enrollment->semester_id,
            'section_id' => $this->enrollment->section_id,
            'homework' => 85
        ];

        $response = $this->postJson("/api/teacher/students/{$this->student->id}/marks", $markData);

        $response->assertStatus(403);
    }

    public function test_validation_requires_semester_id()
    {
        Sanctum::actingAs($this->teacherUser);

        $markData = [
            'subject_id' => $this->subject->id,
            'section_id' => $this->enrollment->section_id,
            'homework' => 85
        ];

        $response = $this->postJson("/api/teacher/students/{$this->student->id}/marks", $markData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['semester_id']);
    }

    public function test_validation_requires_section_id()
    {
        Sanctum::actingAs($this->teacherUser);

        $markData = [
            'subject_id' => $this->subject->id,
            'semester_id' => $this->enrollment->semester_id,
            'homework' => 85
        ];

        $response = $this->postJson("/api/teacher/students/{$this->student->id}/marks", $markData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['section_id']);
    }

    public function test_validation_requires_valid_semester_id()
    {
        Sanctum::actingAs($this->teacherUser);

        $markData = [
            'subject_id' => $this->subject->id,
            'semester_id' => 99999, // Non-existent semester
            'section_id' => $this->enrollment->section_id,
            'homework' => 85
        ];

        $response = $this->postJson("/api/teacher/students/{$this->student->id}/marks", $markData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['semester_id']);
    }

    public function test_validation_requires_valid_section_id()
    {
        Sanctum::actingAs($this->teacherUser);

        $markData = [
            'subject_id' => $this->subject->id,
            'semester_id' => $this->enrollment->semester_id,
            'section_id' => 99999, // Non-existent section
            'homework' => 85
        ];

        $response = $this->postJson("/api/teacher/students/{$this->student->id}/marks", $markData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['section_id']);
    }
}
