<?php

namespace App\Http\Requests\HomepageCtaSection;

use App\Status;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateHomepageCtaSectionRequest extends FormRequest
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
            'title' => ['nullable', 'string', 'max:255'],
            'sub_title' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'button_text' => ['nullable', 'string', 'max:255'],
            'button_link' => ['nullable', 'string', 'url', 'max:500'],
            'status' => ['nullable', Rule::enum(Status::class)],
        ];
    }
}
