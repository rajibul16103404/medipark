<?php

namespace App\Http\Requests\Homepage;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHomepageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'hero_title' => ['sometimes', 'string', 'max:255'],
            'hero_subtitle' => ['sometimes', 'string', 'max:255'],
            'hero_background_image' => ['sometimes', 'string', 'max:500'],
            'about_title' => ['sometimes', 'string', 'max:255'],
            'about_content' => ['sometimes', 'string'],
            'about_images' => ['sometimes', 'array'],
            'about_images.*' => ['string', 'max:500'],
            'doctors_title' => ['sometimes', 'string', 'max:255'],
            'doctors_description' => ['sometimes', 'string'],
            'pricing_title' => ['sometimes', 'string', 'max:255'],
            'pricing_description' => ['sometimes', 'string'],
            'news_title' => ['sometimes', 'string', 'max:255'],
            'news_description' => ['sometimes', 'string'],
            'ask_title' => ['sometimes', 'string', 'max:255'],
            'ask_subtitle' => ['sometimes', 'string', 'max:500'],
            'ask_button_text' => ['sometimes', 'string', 'max:100'],
            'blog_title' => ['sometimes', 'string', 'max:255'],
            'blog_description' => ['sometimes', 'string'],
            'investor_title' => ['sometimes', 'string', 'max:255'],
            'investor_description' => ['sometimes', 'string'],
            'footer_contact' => ['sometimes', 'array'],
            'footer_links' => ['sometimes', 'array'],
            'footer_social_links' => ['sometimes', 'array'],
            'footer_copyright' => ['sometimes', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
