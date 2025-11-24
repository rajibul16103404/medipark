<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class VerifyOtpRequest extends FormRequest
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
            'otp' => ['required', 'string', 'size:6', 'regex:/^[0-9]{6}$/'],
            'type' => ['sometimes', 'nullable', 'string', 'max:50', 'regex:/^[a-z0-9_-]+$/i'],
            'auto_use' => ['sometimes', 'nullable', 'boolean'],
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
            'otp.required' => 'The OTP field is required.',
            'otp.string' => 'The OTP must be a string.',
            'otp.size' => 'The OTP must be exactly 6 digits.',
            'otp.regex' => 'The OTP must contain only numbers.',
            'type.string' => 'The type must be a string.',
            'type.max' => 'The type may not be greater than 50 characters.',
            'type.regex' => 'The type may only contain letters, numbers, dashes, and underscores.',
            'auto_use.boolean' => 'The auto_use field must be true or false.',
        ];
    }
}
