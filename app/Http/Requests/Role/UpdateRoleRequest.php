<?php

namespace App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoleRequest extends FormRequest
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
        $roleId = $this->route('role')->id;

        return [
            'name' => ['sometimes', 'required', 'string', 'min:2', 'max:255'],
            'slug' => ['sometimes', 'required', 'string', 'min:2', 'max:255', 'regex:/^[a-z0-9_-]+$/', Rule::unique('roles', 'slug')->ignore($roleId)],
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
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.min' => 'The name must be at least 2 characters.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'slug.required' => 'The slug field is required.',
            'slug.string' => 'The slug must be a string.',
            'slug.min' => 'The slug must be at least 2 characters.',
            'slug.max' => 'The slug may not be greater than 255 characters.',
            'slug.regex' => 'The slug may only contain lowercase letters, numbers, dashes, and underscores.',
            'slug.unique' => 'This slug is already taken.',
        ];
    }
}
