<?php

namespace Tests\Feature;

use App\Enums\Platform;
use App\Enums\UserType;
use App\Models\AppUpdate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $teacher;
    protected $student;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test users
        $this->admin = User::factory()->create(['user_type' => UserType::Admin->value]);
        $this->teacher = User::factory()->create(['user_type' => UserType::Teacher->value]);
        $this->student = User::factory()->create(['user_type' => UserType::Student->value]);
    }

    /** @test */
    public function admin_can_create_app_update()
    {
        $response = $this->actingAs($this->admin, 'api')
            ->postJson('/api/admin/app-updates', [
                'version' => '1.2.23',
                'platform' => Platform::Android->value,
                'url' => 'https://example.com/app-v1.2.23.apk',
                'change_log' => 'Bug fixes and performance improvements',
                'is_force_update' => true,
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'App update created successfully',
                'data' => [
                    'version' => '1.2.23',
                    'platform' => Platform::Android->value,
                    'url' => 'https://example.com/app-v1.2.23.apk',
                    'change_log' => 'Bug fixes and performance improvements',
                    'is_force_update' => true,
                ]
            ]);

        $this->assertDatabaseHas('app_updates', [
            'version' => '1.2.23',
            'platform' => Platform::Android->value,
        ]);
    }

    /** @test */
    public function admin_can_update_app_update()
    {
        $appUpdate = AppUpdate::factory()->create([
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin, 'api')
            ->putJson("/api/admin/app-updates/{$appUpdate->id}", [
                'version' => '1.2.24',
                'change_log' => 'Updated change log',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'App update updated successfully',
                'data' => [
                    'version' => '1.2.24',
                    'change_log' => 'Updated change log',
                ]
            ]);
    }

    /** @test */
    public function admin_can_list_app_updates()
    {
        AppUpdate::factory()->count(3)->create([
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin, 'api')
            ->getJson('/api/admin/app-updates');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'App updates retrieved successfully',
            ]);

        $this->assertCount(3, $response->json('data.updates'));
    }

    /** @test */
    public function admin_can_filter_app_updates_by_platform()
    {
        AppUpdate::factory()->create([
            'platform' => Platform::Android->value,
            'created_by' => $this->admin->id,
        ]);
        AppUpdate::factory()->create([
            'platform' => Platform::IOS->value,
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin, 'api')
            ->getJson('/api/admin/app-updates?platform=' . Platform::Android->value);

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data.updates'));
        $this->assertEquals(Platform::Android->value, $response->json('data.updates.0.platform'));
    }

    /** @test */
    public function admin_can_list_trashed_app_updates()
    {
        // Create and soft delete some app updates
        $trashedUpdate1 = AppUpdate::factory()->create([
            'created_by' => $this->admin->id,
        ]);
        $trashedUpdate2 = AppUpdate::factory()->create([
            'created_by' => $this->admin->id,
        ]);

        // Soft delete them
        $trashedUpdate1->delete();
        $trashedUpdate2->delete();

        $response = $this->actingAs($this->admin, 'api')
            ->getJson('/api/admin/app-updates/trashed/list');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Trashed app updates retrieved successfully',
            ]);

        $this->assertCount(2, $response->json('data.trashed_updates'));
    }

    /** @test */
    public function admin_can_filter_trashed_app_updates_by_platform()
    {
        // Create and soft delete app updates for different platforms
        $androidUpdate = AppUpdate::factory()->create([
            'platform' => Platform::Android->value,
            'created_by' => $this->admin->id,
        ]);
        $iosUpdate = AppUpdate::factory()->create([
            'platform' => Platform::IOS->value,
            'created_by' => $this->admin->id,
        ]);

        // Soft delete them
        $androidUpdate->delete();
        $iosUpdate->delete();

        $response = $this->actingAs($this->admin, 'api')
            ->getJson('/api/admin/app-updates/trashed/list?platform=' . Platform::Android->value);

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data.trashed_updates'));
        $this->assertEquals(Platform::Android->value, $response->json('data.trashed_updates.0.platform'));
    }

    /** @test */
    public function admin_can_delete_app_update()
    {
        $appUpdate = AppUpdate::factory()->create([
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin, 'api')
            ->deleteJson("/api/admin/app-updates/{$appUpdate->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'App update deleted successfully',
            ]);

        $this->assertSoftDeleted('app_updates', ['id' => $appUpdate->id]);
    }

    /** @test */
    public function teacher_can_check_for_app_updates()
    {
        AppUpdate::factory()->create([
            'version' => '1.2.23',
            'platform' => Platform::Android->value,
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->teacher, 'api')
            ->postJson('/api/app-updates/check', [
                'version' => '1.2.20',
                'platform' => Platform::Android->value,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Update check completed successfully',
                'data' => [
                    'has_update' => true,
                    'last_version' => '1.2.23',
                ]
            ]);
    }

    /** @test */
    public function student_can_check_for_app_updates()
    {
        AppUpdate::factory()->create([
            'version' => '1.2.23',
            'platform' => Platform::IOS->value,
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->student, 'api')
            ->postJson('/api/app-updates/check', [
                'version' => '1.2.23',
                'platform' => Platform::IOS->value,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'No updates available',
                'data' => [
                    'has_update' => false,
                ]
            ]);
    }

    /** @test */
    public function non_admin_users_cannot_create_app_updates()
    {
        $response = $this->actingAs($this->teacher, 'api')
            ->postJson('/api/admin/app-updates', [
                'version' => '1.2.23',
                'platform' => Platform::Android->value,
                'url' => 'https://example.com/app-v1.2.23.apk',
                'change_log' => 'Bug fixes',
                'is_force_update' => false,
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function non_admin_users_cannot_update_app_updates()
    {
        $appUpdate = AppUpdate::factory()->create([
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->teacher, 'api')
            ->putJson("/api/admin/app-updates/{$appUpdate->id}", [
                'version' => '1.2.24',
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function validation_works_for_invalid_version_format()
    {
        $response = $this->actingAs($this->admin, 'api')
            ->postJson('/api/admin/app-updates', [
                'version' => 'invalid-version',
                'platform' => Platform::Android->value,
                'url' => 'https://example.com/app.apk',
                'change_log' => 'Bug fixes',
                'is_force_update' => false,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['version']);
    }

    /** @test */
    public function validation_works_for_invalid_platform()
    {
        $response = $this->actingAs($this->admin, 'api')
            ->postJson('/api/admin/app-updates', [
                'version' => '1.2.23',
                'platform' => 'invalid-platform',
                'url' => 'https://example.com/app.apk',
                'change_log' => 'Bug fixes',
                'is_force_update' => false,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['platform']);
    }

    /** @test */
    public function validation_works_for_invalid_url()
    {
        $response = $this->actingAs($this->admin, 'api')
            ->postJson('/api/admin/app-updates', [
                'version' => '1.2.23',
                'platform' => Platform::Android->value,
                'url' => 'not-a-valid-url',
                'change_log' => 'Bug fixes',
                'is_force_update' => false,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['url']);
    }
}
