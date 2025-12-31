<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FutureVenture>
 */
class FutureVentureFactory extends Factory
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
            'description' => fake()->paragraph(3),
            'image' => fake()->imageUrl(),
        ];
    }
}
