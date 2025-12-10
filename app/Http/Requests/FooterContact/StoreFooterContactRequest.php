<?php

namespace App\Http\Requests\FooterContact;

use App\Status;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFooterContactRequest extends FormRequest
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
            'email' => ['required', 'string', 'email', 'max:255'],
            'phone' => ['nullable', 'array'],
            'phone.*' => ['string', 'max:20'],
            'status' => ['nullable', Rule::enum(Status::class)],
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
            'email.email' => 'The email must be a valid email address.',
            'phone.array' => 'The phone must be an array.',
            'phone.*.string' => 'Each phone number must be a string.',
            'phone.*.max' => 'Each phone number must not exceed 20 characters.',
        ];
    }
}
