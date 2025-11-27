<?php

namespace App\Http\Requests\Homepage;

use Illuminate\Foundation\Http\FormRequest;

class CreateHomepageRequest extends FormRequest
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
            'hero_title' => ['nullable', 'string', 'max:255'],
            'hero_subtitle' => ['nullable', 'string', 'max:255'],
            'hero_background_image' => ['nullable', 'string', 'max:500'],
            'about_title' => ['nullable', 'string', 'max:255'],
            'about_content' => ['nullable', 'string'],
            'about_images' => ['nullable', 'array'],
            'about_images.*' => ['string', 'max:500'],
            'doctors_title' => ['nullable', 'string', 'max:255'],
            'doctors_description' => ['nullable', 'string'],
            'pricing_title' => ['nullable', 'string', 'max:255'],
            'pricing_description' => ['nullable', 'string'],
            'news_title' => ['nullable', 'string', 'max:255'],
            'news_description' => ['nullable', 'string'],
            'ask_title' => ['nullable', 'string', 'max:255'],
            'ask_subtitle' => ['nullable', 'string', 'max:500'],
            'ask_button_text' => ['nullable', 'string', 'max:100'],
            'blog_title' => ['nullable', 'string', 'max:255'],
            'blog_description' => ['nullable', 'string'],
            'investor_title' => ['nullable', 'string', 'max:255'],
            'investor_description' => ['nullable', 'string'],
            'footer_contact' => ['nullable', 'array'],
            'footer_links' => ['nullable', 'array'],
            'footer_social_links' => ['nullable', 'array'],
            'footer_copyright' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
