<?php

namespace Database\Factories;

use App\Enums\Platform;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AppUpdate>
 */
class AppUpdateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $platforms = Platform::values();
        $platform = $platforms[array_rand($platforms)];
        
        $major = $this->faker->numberBetween(1, 10);
        $minor = $this->faker->numberBetween(0, 99);
        $patch = $this->faker->numberBetween(0, 99);
        $version = "{$major}.{$minor}.{$patch}";

        $urls = [
            Platform::Android->value => "https://example.com/app-v{$version}.apk",
            Platform::IOS->value => "https://apps.apple.com/app/id" . $this->faker->numberBetween(100000000, 999999999),
        ];

        return [
            'version' => $version,
            'platform' => $platform,
            'url' => $urls[$platform],
            'change_log' => $this->faker->paragraph(),
            'is_force_update' => $this->faker->boolean(20), // 20% chance of being force update
            'created_by' => User::factory(),
        ];
    }

    /**
     * Indicate that the app update is for Android platform.
     */
    public function android(): static
    {
        return $this->state(fn (array $attributes) => [
            'platform' => Platform::Android->value,
            'url' => "https://example.com/app-v{$attributes['version']}.apk",
        ]);
    }

    /**
     * Indicate that the app update is for iOS platform.
     */
    public function ios(): static
    {
        return $this->state(fn (array $attributes) => [
            'platform' => Platform::IOS->value,
            'url' => "https://apps.apple.com/app/id" . $this->faker->numberBetween(100000000, 999999999),
        ]);
    }



    /**
     * Indicate that the app update is a force update.
     */
    public function forceUpdate(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_force_update' => true,
        ]);
    }

    /**
     * Indicate that the app update is not a force update.
     */
    public function notForceUpdate(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_force_update' => false,
        ]);
    }
}
