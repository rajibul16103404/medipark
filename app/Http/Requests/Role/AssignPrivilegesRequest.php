<?php

namespace App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignPrivilegesRequest extends FormRequest
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
            'privilege_ids' => ['required', 'array', 'min:1'],
            'privilege_ids.*' => ['required', 'integer', 'min:1', Rule::exists('privileges', 'id')],
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
            'privilege_ids.required' => 'The privilege_ids field is required.',
            'privilege_ids.array' => 'The privilege_ids must be an array.',
            'privilege_ids.min' => 'At least one privilege ID is required.',
            'privilege_ids.*.required' => 'Each privilege ID is required.',
            'privilege_ids.*.integer' => 'Each privilege ID must be an integer.',
            'privilege_ids.*.min' => 'Each privilege ID must be at least 1.',
            'privilege_ids.*.exists' => 'One or more privilege IDs do not exist.',
        ];
    }
}
