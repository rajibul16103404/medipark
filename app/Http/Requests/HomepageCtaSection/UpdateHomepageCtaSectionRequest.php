<?php

namespace App\Http\Requests\HomepageCtaSection;

use App\Status;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateHomepageCtaSectionRequest extends FormRequest
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
            'title' => ['sometimes', 'string', 'max:255'],
            'sub_title' => ['sometimes', 'string', 'max:255'],
            'content' => ['sometimes', 'string'],
            'button_text' => ['sometimes', 'string', 'max:255'],
            'button_link' => ['sometimes', 'string', 'url', 'max:500'],
            'status' => ['sometimes', Rule::enum(Status::class)],
        ];
    }
}
