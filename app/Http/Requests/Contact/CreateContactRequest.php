<?php

namespace App\Http\Requests\Contact;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateContactRequest extends FormRequest
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
            'patient_name' => ['required', 'string', 'max:255'],
            'gender' => ['nullable', 'string', Rule::in(['male', 'female', 'other'])],
            'phone_number' => ['required', 'string', 'max:20'],
            'email' => ['required', 'string', 'email:rfc,dns', 'max:255'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'message' => ['required', 'string'],
        ];
    }
}
