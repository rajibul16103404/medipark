<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class SuspendUserRequest extends FormRequest
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
            'reason' => ['required', 'string', 'min:3', 'max:500'],
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
            'reason.required' => 'The suspension reason is required.',
            'reason.string' => 'The suspension reason must be a string.',
            'reason.min' => 'The suspension reason must be at least 3 characters.',
            'reason.max' => 'The suspension reason may not be greater than 500 characters.',
        ];
    }
}
