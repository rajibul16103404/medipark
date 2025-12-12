<?php

namespace App\Http\Requests\InstallmentRule;

use App\Status;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInstallmentRuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'min:2', 'max:255'],
            'duration_months' => ['sometimes', 'integer', 'min:1', 'max:120'],
            'status' => ['sometimes', Rule::enum(Status::class)],
            'description' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => 'The name must be a string.',
            'name.min' => 'The name must be at least 2 characters.',
            'name.max' => 'The name must not exceed 255 characters.',
            'duration_months.integer' => 'The duration must be an integer.',
            'duration_months.min' => 'The duration must be at least 1 month.',
            'duration_months.max' => 'The duration must not exceed 120 months.',
            'status.enum' => 'The status must be a valid status.',
        ];
    }
}
