<?php

namespace Database\Factories;

use App\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Facility>
 */
class FacilityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'short_description' => fake()->sentence(10),
            'image' => fake()->imageUrl(),
            'status' => fake()->randomElement([Status::Active->value, Status::Inactive->value]),
        ];
    }
}
