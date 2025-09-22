<?php

namespace Tests\Feature;

use App\Enums\WeekDay;
use App\Models\ClassPeriod;
use App\Models\Grade;
use App\Models\Schedule;
use App\Models\SchoolShift;
use App\Models\SchoolShiftTarget;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeacherSectionSubject;
use App\Models\TimeTable;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class WeeklyScheduleManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $section;
    protected $timetable;
    protected $classPeriod;
    protected $teacherSectionSubject;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user
        $this->admin = User::factory()->create([
            'user_type' => 'admin'
        ]);

        // Create grade
        $grade = Grade::factory()->create();

        // Create section
        $this->section = Section::factory()->create([
            'grade_id' => $grade->id
        ]);

        // Create school shift
        $schoolShift = SchoolShift::factory()->create();

        // Create school shift target
        SchoolShiftTarget::factory()->create([
            'school_shift_id' => $schoolShift->id,
            'section_id' => $this->section->id
        ]);

        // Create class period
        $this->classPeriod = ClassPeriod::factory()->create([
            'school_shift_id' => $schoolShift->id,
            'period_order' => 1
        ]);

        // Create timetable
        $this->timetable = TimeTable::factory()->create([
            'is_active' => true
        ]);

        // Create teacher
        $teacher = Teacher::factory()->create();

        // Create subject
        $subject = Subject::factory()->create();

        // Create teacher section subject
        $this->teacherSectionSubject = TeacherSectionSubject::factory()->create([
            'teacher_id' => $teacher->id,
            'section_id' => $this->section->id,
            'subject_id' => $subject->id,
            'grade_id' => $grade->id,
            'is_active' => true
        ]);

        Sanctum::actingAs($this->admin);
    }

    /** @test */
    public function admin_can_get_schedules_for_section()
    {
        $response = $this->getJson('/api/admin/schedules', [
            'section_id' => $this->section->id,
            'timetable_id' => $this->timetable->id
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'successful',
                    'message',
                    'data' => [
                        'section_id',
                        'timetable_id',
                        'schedules',
                        'week_days',
                        'class_periods'
                    ]
                ]);
    }

    /** @test */
    public function admin_can_filter_schedules_by_week_day()
    {
        $response = $this->getJson('/api/admin/schedules', [
            'section_id' => $this->section->id,
            'timetable_id' => $this->timetable->id,
            'week_day' => 'sunday'
        ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_create_bulk_schedules()
    {
        $data = [
            'section_id' => $this->section->id,
            'timetable_id' => $this->timetable->id,
            'schedules' => [
                [
                    'class_period_id' => $this->classPeriod->id,
                    'teacher_section_subject_id' => $this->teacherSectionSubject->id,
                    'week_day' => 'sunday'
                ]
            ]
        ];

        $response = $this->postJson('/api/admin/schedules/bulk', $data);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'successful',
                    'message',
                    'data' => [
                        'section_id',
                        'timetable_id',
                        'created_count',
                        'schedules'
                    ]
                ]);

        $this->assertDatabaseHas('schedules', [
            'section_id' => $this->section->id,
            'timetable_id' => $this->timetable->id,
            'class_period_id' => $this->classPeriod->id,
            'teacher_section_subject_id' => $this->teacherSectionSubject->id,
            'week_day' => 'sunday'
        ]);
    }

    /** @test */
    public function admin_can_update_bulk_schedules()
    {
        // Create existing schedule
        $schedule = Schedule::factory()->create([
            'class_period_id' => $this->classPeriod->id,
            'teacher_section_subject_id' => $this->teacherSectionSubject->id,
            'timetable_id' => $this->timetable->id,
            'week_day' => 'sunday',
            'created_by' => $this->admin->id
        ]);

        $data = [
            'section_id' => $this->section->id,
            'timetable_id' => $this->timetable->id,
            'schedules' => [
                [
                    'id' => $schedule->id,
                    'class_period_id' => $this->classPeriod->id,
                    'teacher_section_subject_id' => $this->teacherSectionSubject->id,
                    'week_day' => 'monday'
                ]
            ]
        ];

        $response = $this->putJson('/api/admin/schedules/bulk', $data);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'successful',
                    'message',
                    'data' => [
                        'section_id',
                        'timetable_id',
                        'updated_count',
                        'created_count',
                        'deleted_count',
                        'schedules'
                    ]
                ]);

        $this->assertDatabaseHas('schedules', [
            'id' => $schedule->id,
            'week_day' => 'monday'
        ]);
    }

    /** @test */
    public function validation_fails_with_invalid_section_id()
    {
        $data = [
            'section_id' => 99999,
            'timetable_id' => $this->timetable->id,
            'schedules' => [
                [
                    'class_period_id' => $this->classPeriod->id,
                    'teacher_section_subject_id' => $this->teacherSectionSubject->id,
                    'week_day' => 'sunday'
                ]
            ]
        ];

        $response = $this->postJson('/api/admin/schedules/bulk', $data);

        $response->assertStatus(400);
    }

    /** @test */
    public function validation_fails_with_invalid_week_day()
    {
        $data = [
            'section_id' => $this->section->id,
            'timetable_id' => $this->timetable->id,
            'schedules' => [
                [
                    'class_period_id' => $this->classPeriod->id,
                    'teacher_section_subject_id' => $this->teacherSectionSubject->id,
                    'week_day' => 'invalid_day'
                ]
            ]
        ];

        $response = $this->postJson('/api/admin/schedules/bulk', $data);

        $response->assertStatus(422);
    }

    /** @test */
    public function non_admin_user_cannot_access_schedules()
    {
        $regularUser = User::factory()->create([
            'user_type' => 'teacher'
        ]);

        Sanctum::actingAs($regularUser);

        $response = $this->getJson('/api/admin/schedules', [
            'section_id' => $this->section->id,
            'timetable_id' => $this->timetable->id
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function schedules_are_organized_by_week_days()
    {
        // Create schedules for different days
        Schedule::factory()->create([
            'class_period_id' => $this->classPeriod->id,
            'teacher_section_subject_id' => $this->teacherSectionSubject->id,
            'timetable_id' => $this->timetable->id,
            'week_day' => 'sunday',
            'created_by' => $this->admin->id
        ]);

        Schedule::factory()->create([
            'class_period_id' => $this->classPeriod->id,
            'teacher_section_subject_id' => $this->teacherSectionSubject->id,
            'timetable_id' => $this->timetable->id,
            'week_day' => 'monday',
            'created_by' => $this->admin->id
        ]);

        $response = $this->getJson('/api/admin/schedules', [
            'section_id' => $this->section->id,
            'timetable_id' => $this->timetable->id
        ]);

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertCount(7, $data['week_days']); // All 7 days of the week
        
        // Check that Sunday and Monday have schedules
        $sunday = collect($data['week_days'])->firstWhere('name', 'Sunday');
        $monday = collect($data['week_days'])->firstWhere('name', 'Monday');
        
        $this->assertNotEmpty($sunday['schedules']);
        $this->assertNotEmpty($monday['schedules']);
    }

    /** @test */
    public function class_periods_are_included_in_response()
    {
        $response = $this->getJson('/api/admin/schedules', [
            'section_id' => $this->section->id,
            'timetable_id' => $this->timetable->id
        ]);

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertNotEmpty($data['class_periods']);
        
        $classPeriod = $data['class_periods'][0];
        $this->assertArrayHasKey('id', $classPeriod);
        $this->assertArrayHasKey('name', $classPeriod);
        $this->assertArrayHasKey('start_time', $classPeriod);
        $this->assertArrayHasKey('end_time', $classPeriod);
        $this->assertArrayHasKey('period_order', $classPeriod);
        $this->assertArrayHasKey('duration_minutes', $classPeriod);
    }
}
