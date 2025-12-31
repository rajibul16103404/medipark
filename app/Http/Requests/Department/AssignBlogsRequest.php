<?php

namespace App\Http\Requests\Department;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignBlogsRequest extends FormRequest
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
            'blog_ids' => ['nullable', 'array'],
            'blog_ids.*' => ['required', 'integer', Rule::exists('blogs', 'id')],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'blog_ids.array' => 'The blog_ids must be an array.',
            'blog_ids.*.required' => 'Each blog ID is required.',
            'blog_ids.*.integer' => 'Each blog ID must be an integer.',
            'blog_ids.*.exists' => 'One or more selected blogs do not exist.',
        ];
    }
}
