<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Homepage extends Model
{
    use HasFactory;

    protected $fillable = [
        'hero_title',
        'hero_subtitle',
        'hero_background_image',
        'about_title',
        'about_content',
        'about_images',
        'doctors_title',
        'doctors_description',
        'pricing_title',
        'pricing_description',
        'news_title',
        'news_description',
        'ask_title',
        'ask_subtitle',
        'ask_button_text',
        'blog_title',
        'blog_description',
        'investor_title',
        'investor_description',
        'footer_contact',
        'footer_links',
        'footer_social_links',
        'footer_copyright',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'about_images' => 'array',
            'footer_contact' => 'array',
            'footer_links' => 'array',
            'footer_social_links' => 'array',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the active homepage.
     */
    public static function active(): ?self
    {
        return static::where('is_active', true)->first();
    }
}
