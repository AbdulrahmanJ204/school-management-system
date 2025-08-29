<?php

namespace Tests\Feature;

use App\Enums\ClassPeriodType;
use App\Models\ClassPeriod;
use App\Models\Grade;
use App\Models\SchoolShift;
use App\Models\SchoolShiftTarget;
use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminClassPeriodsApiTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $section;
    protected $schoolShift;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user
        $this->admin = User::factory()->create([
            'user_type' => 'admin'
        ]);

        // Create grade
        $grade = Grade::create([
            'title' => 'الصف الأول',
            'created_by' => $this->admin->id
        ]);

        // Create section
        $this->section = Section::create([
            'title' => 'الشعبة أ',
            'grade_id' => $grade->id,
            'created_by' => $this->admin->id
        ]);

        // Create school shift
        $this->schoolShift = SchoolShift::create([
            'name' => 'الفترة الصباحية',
            'start_time' => '08:00:00',
            'end_time' => '14:00:00',
            'is_active' => true,
            'created_by' => $this->admin->id
        ]);

        // Create school shift target
        SchoolShiftTarget::create([
            'school_shift_id' => $this->schoolShift->id,
            'section_id' => $this->section->id,
            'created_by' => $this->admin->id
        ]);

        // Create class periods
        ClassPeriod::create([
            'name' => 'الحصة الأولى',
            'start_time' => '08:00:00',
            'end_time' => '08:45:00',
            'school_shift_id' => $this->schoolShift->id,
            'period_order' => 1,
            'type' => ClassPeriodType::STUDY,
            'duration_minutes' => 45,
            'created_by' => $this->admin->id
        ]);

        ClassPeriod::create([
            'name' => 'الحصة الثانية',
            'start_time' => '08:45:00',
            'end_time' => '09:30:00',
            'school_shift_id' => $this->schoolShift->id,
            'period_order' => 2,
            'type' => ClassPeriodType::STUDY,
            'duration_minutes' => 45,
            'created_by' => $this->admin->id
        ]);

        ClassPeriod::create([
            'name' => 'استراحة',
            'start_time' => '09:30:00',
            'end_time' => '09:45:00',
            'school_shift_id' => $this->schoolShift->id,
            'period_order' => 3,
            'type' => ClassPeriodType::BREAK,
            'duration_minutes' => 15,
            'created_by' => $this->admin->id
        ]);

        Sanctum::actingAs($this->admin);
    }

    /** @test */
    public function admin_can_get_study_class_periods_by_section()
    {
        $response = $this->getJson('/api/admin/class-periods?section_id=' . $this->section->id);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'successful',
                    'message',
                    'data' => [
                        'class_periods' => [
                            '*' => [
                                'id',
                                'name',
                                'start_time',
                                'end_time',
                                'period_order',
                                'duration_minutes',
                                'school_shift' => [
                                    'id',
                                    'name',
                                    'start_time',
                                    'end_time'
                                ]
                            ]
                        ]
                    ],
                    'status_code'
                ])
                ->assertJson([
                    'message' => 'قائمة الحصص الدراسية للشعبة',
                    'status_code' => 200
                ]);

        // Verify only study periods are returned (not break periods)
        $data = $response->json('data.class_periods');
        $this->assertCount(2, $data); // Only 2 study periods, not 3 total periods
        
        // Verify periods are ordered by period_order
        $this->assertEquals(1, $data[0]['period_order']);
        $this->assertEquals(2, $data[1]['period_order']);
    }

    /** @test */
    public function returns_404_for_nonexistent_section()
    {
        $response = $this->getJson('/api/admin/class-periods?section_id=999');

        $response->assertStatus(404)
                ->assertJson([
                    'message' => 'الشعبة غير موجودة'
                ]);
    }

    /** @test */
    public function returns_422_for_missing_section_id()
    {
        $response = $this->getJson('/api/admin/class-periods');

        $response->assertStatus(422)
                ->assertJson([
                    'message' => 'معرف الشعبة مطلوب'
                ]);
    }

    /** @test */
    public function returns_422_for_invalid_section_id()
    {
        $response = $this->getJson('/api/admin/class-periods?section_id=invalid');

        $response->assertStatus(422);
    }

    /** @test */
    public function returns_empty_array_when_no_study_periods_found()
    {
        // Create a new section without any class periods
        $newGrade = Grade::create([
            'title' => 'الصف الثاني',
            'created_by' => $this->admin->id
        ]);
        
        $newSection = Section::create([
            'title' => 'الشعبة ب',
            'grade_id' => $newGrade->id,
            'created_by' => $this->admin->id
        ]);

        $response = $this->getJson('/api/admin/class-periods?section_id=' . $newSection->id);

        $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'class_periods' => []
                    ],
                    'message' => 'لا توجد حصص دراسية للشعبة المحددة'
                ]);
    }
}
