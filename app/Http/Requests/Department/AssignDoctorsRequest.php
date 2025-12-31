<?php

namespace App\Http\Requests\Department;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignDoctorsRequest extends FormRequest
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
            'doctor_ids' => ['nullable', 'array'],
            'doctor_ids.*' => ['required', 'integer', Rule::exists('doctors', 'id')],
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
            'doctor_ids.array' => 'The doctor_ids must be an array.',
            'doctor_ids.*.required' => 'Each doctor ID is required.',
            'doctor_ids.*.integer' => 'Each doctor ID must be an integer.',
            'doctor_ids.*.exists' => 'One or more selected doctors do not exist.',
        ];
    }
}
