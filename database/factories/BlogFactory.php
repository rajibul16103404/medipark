<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Blog>
 */
class BlogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(3),
            'feature_image' => fake()->imageUrl(),
            'status' => fake()->randomElement(['active', 'inactive']),
            'author_name' => fake()->name(),
            'author_image' => fake()->imageUrl(),
            'author_designation' => fake()->jobTitle(),
        ];
    }
}
