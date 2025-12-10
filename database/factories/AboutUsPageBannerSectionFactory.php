<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AboutUsPageBannerSection>
 */
class AboutUsPageBannerSectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'background_image' => 'http://example.com/about-banner.jpg',
            'opacity' => '0.5',
            'status' => 'active',
        ];
    }
}
