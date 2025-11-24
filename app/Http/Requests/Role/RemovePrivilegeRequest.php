<?php

namespace App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RemovePrivilegeRequest extends FormRequest
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
            'privilege_id' => ['required', 'integer', 'min:1', Rule::exists('privileges', 'id')],
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
            'privilege_id.required' => 'The privilege_id field is required.',
            'privilege_id.integer' => 'The privilege_id must be an integer.',
            'privilege_id.min' => 'The privilege_id must be at least 1.',
            'privilege_id.exists' => 'The selected privilege does not exist.',
        ];
    }
}
