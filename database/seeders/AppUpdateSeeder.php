<?php

namespace Database\Seeders;

use App\Enums\Platform;
use App\Models\AppUpdate;
use App\Models\User;
use Illuminate\Database\Seeder;

class AppUpdateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first admin user or create one
        $admin = User::where('user_type', 'admin')->first();
        
        if (!$admin) {
            $admin = User::factory()->create([
                'user_type' => 'admin',
                'email' => 'admin@example.com'
            ]);
        }

        // Create sample app updates
        $appUpdates = [
            [
                'version' => '1.2.23',
                'platform' => Platform::Android->value,
                'url' => 'https://example.com/app-v1.2.23.apk',
                'change_log' => 'إصلاح الأخطاء وتحسين الأداء',
                'is_force_update' => true,
                'created_by' => $admin->id,
            ],
            [
                'version' => '1.2.20',
                'platform' => Platform::Android->value,
                'url' => 'https://example.com/app-v1.2.20.apk',
                'change_log' => 'إصلاح أخطاء بسيطة',
                'is_force_update' => false,
                'created_by' => $admin->id,
            ],
            [
                'version' => '1.2.15',
                'platform' => Platform::IOS->value,
                'url' => 'https://apps.apple.com/app/id123456789',
                'change_log' => 'ميزات جديدة وتحسينات',
                'is_force_update' => false,
                'created_by' => $admin->id,
            ],
            [
                'version' => '1.2.10',
                'platform' => Platform::IOS->value,
                'url' => 'https://apps.apple.com/app/id987654321',
                'change_log' => 'تحسينات إضافية لنظام iOS',
                'is_force_update' => false,
                'created_by' => $admin->id,
            ],
        ];

        foreach ($appUpdates as $appUpdate) {
            AppUpdate::create($appUpdate);
        }
    }
}
