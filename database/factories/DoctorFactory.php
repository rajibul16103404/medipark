<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Doctor>
 */
class DoctorFactory extends Factory
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
        return [
            'doctor_identity_number' => 'DOC'.str_pad((string) fake()->unique()->numberBetween(1, 999999), 6, '0', STR_PAD_LEFT),
            'doctor_name' => fake()->name(),
            'department' => fake()->randomElement(['Cardiology', 'Neurology', 'Pediatrics', 'Orthopedics', 'Dermatology', 'Oncology']),
            'specialist' => fake()->randomElement(['Cardiologist', 'Neurologist', 'Pediatrician', 'Orthopedic Surgeon', 'Dermatologist', 'Oncologist']),
            'email_address' => fake()->unique()->safeEmail(),
            'mobile_number' => fake()->phoneNumber(),
            'gender' => fake()->randomElement(['male', 'female', 'other']),
            'date_of_birth' => fake()->date('Y-m-d', '-30 years'),
            'known_languages' => fake()->randomElements(['English', 'Bengali', 'Hindi', 'Arabic', 'Spanish'], fake()->numberBetween(1, 3)),
            'registration_number' => fake()->unique()->numerify('REG-#######'),
            'about' => fake()->paragraph(),
            'present_address' => fake()->address(),
            'permanent_address' => fake()->address(),
            'display_name' => fake()->name(),
            'user_name' => fake()->unique()->userName(),
            'password' => static::$password ??= Hash::make('password'),
        ];
    }
}
