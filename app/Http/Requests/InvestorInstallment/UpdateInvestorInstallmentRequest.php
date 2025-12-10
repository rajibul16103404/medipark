<?php

namespace App\Http\Requests\InvestorInstallment;

use App\InstallmentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInvestorInstallmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $installment = $this->route('investorInstallment');
        $dueDate = $this->input('due_date') ?? ($installment?->due_date?->toDateString());

        return [
            'investor_id' => ['sometimes', 'exists:investors,id'],
            'installment_number' => ['sometimes', 'integer', 'min:1'],
            'amount' => ['sometimes', 'numeric', 'min:0', 'max:999999999.99'],
            'due_date' => ['sometimes', 'date'],
            'paid_date' => [
                'nullable',
                'date',
                function ($attribute, $value, $fail) use ($dueDate) {
                    if ($value && $dueDate && $value < $dueDate) {
                        $fail('The paid date must be on or after the due date.');
                    }
                },
            ],
            'status' => ['sometimes', Rule::enum(InstallmentStatus::class)],
            'payment_method' => ['nullable', 'string', 'max:100'],
            'transaction_reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'investor_id.exists' => 'The selected investor does not exist.',
            'installment_number.integer' => 'The installment number must be an integer.',
            'installment_number.min' => 'The installment number must be at least 1.',
            'amount.numeric' => 'The amount must be a number.',
            'amount.min' => 'The amount must be at least 0.',
            'due_date.date' => 'The due date must be a valid date.',
            'paid_date.date' => 'The paid date must be a valid date.',
            'paid_date.after_or_equal' => 'The paid date must be on or after the due date.',
            'status.enum' => 'The status must be a valid installment status.',
            'payment_method.max' => 'The payment method must not exceed 100 characters.',
            'transaction_reference.max' => 'The transaction reference must not exceed 255 characters.',
        ];
    }
}
