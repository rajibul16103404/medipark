<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class SendOtpRequest extends FormRequest
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
            'email' => ['required', 'string', 'email:rfc,dns', 'max:255'],
            'type' => ['sometimes', 'nullable', 'string', 'max:50', 'regex:/^[a-z0-9_-]+$/i'],
            'expires_in' => ['sometimes', 'nullable', 'integer', 'min:1', 'max:60'],
            'require_user_exists' => ['sometimes', 'nullable', 'boolean'],
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
            'email.required' => 'The email field is required.',
            'email.string' => 'The email must be a string.',
            'email.email' => 'Please provide a valid email address.',
            'email.max' => 'The email may not be greater than 255 characters.',
            'type.string' => 'The type must be a string.',
            'type.max' => 'The type may not be greater than 50 characters.',
            'type.regex' => 'The type may only contain letters, numbers, dashes, and underscores.',
            'expires_in.integer' => 'The expires_in must be an integer.',
            'expires_in.min' => 'The expires_in must be at least 1 minute.',
            'expires_in.max' => 'The expires_in may not be greater than 60 minutes.',
            'require_user_exists.boolean' => 'The require_user_exists field must be true or false.',
        ];
    }
}
