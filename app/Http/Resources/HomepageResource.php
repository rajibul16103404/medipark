<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HomepageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'hero' => [
                'title' => $this->hero_title,
                'subtitle' => $this->hero_subtitle,
                'background_image' => $this->hero_background_image,
            ],
            'about' => [
                'title' => $this->about_title,
                'content' => $this->about_content,
                'images' => $this->about_images ?? [],
            ],
            'doctors' => [
                'title' => $this->doctors_title,
                'description' => $this->doctors_description,
            ],
            'pricing' => [
                'title' => $this->pricing_title,
                'description' => $this->pricing_description,
            ],
            'news' => [
                'title' => $this->news_title,
                'description' => $this->news_description,
            ],
            'ask_medipark' => [
                'title' => $this->ask_title,
                'subtitle' => $this->ask_subtitle,
                'button_text' => $this->ask_button_text,
            ],
            'blog' => [
                'title' => $this->blog_title,
                'description' => $this->blog_description,
            ],
            'investor' => [
                'title' => $this->investor_title,
                'description' => $this->investor_description,
            ],
            'footer' => [
                'contact' => $this->footer_contact ?? [],
                'links' => $this->footer_links ?? [],
                'social_links' => $this->footer_social_links ?? [],
                'copyright' => $this->footer_copyright,
            ],
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
