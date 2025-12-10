<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GalleryPageBannerSection>
 */
class GalleryPageBannerSectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'background_image' => 'http://example.com/gallery-banner.jpg',
            'opacity' => '0.5',
            'status' => 'active',
        ];
    }
}
