<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $gender = $this->faker->randomElement(['male', 'female']);
        $role = $this->faker->randomElement(['admin', 'teacher', 'student']);

        return [
            'first_name' => $this->faker->firstName($gender),
            'father_name' => $this->faker->firstName($gender),
            'last_name' => $this->faker->lastName(),
            'gender' => $gender,
            'birth_date' => $this->faker->date(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->unique()->phoneNumber(),
            'password' => Hash::make('1234567890'),
            'role' => $role,
            'image' => 'user_images/default.png',
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function admin()
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'admin',
            ];
        })->afterCreating(function (User $user) {
            Admin::create([
                'user_id' => $user->id,
                'created_by' => 1, // This could be the admin's own ID or different logic
            ]);
        });
    }

    public function teacher()
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'teacher',
            ];
        })->afterCreating(function (User $user) {
            Teacher::create([
                'user_id' => $user->id,
                'created_by' => 1, // Or specify logic here
            ]);
        });
    }

    public function student()
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'student',
            ];
        })->afterCreating(function (User $user) {
            Student::create([
                'user_id' => $user->id,
                'created_by' => 1,
                'grandfather' => $this->faker->lastName,
                'general_id' => $this->faker->unique()->numerify('#######'),
                'is_active' => $this->faker->boolean
            ]);
        });
    }
}
