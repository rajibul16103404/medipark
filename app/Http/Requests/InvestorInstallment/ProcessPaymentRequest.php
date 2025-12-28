<?php

namespace App\Http\Requests\InvestorInstallment;

use Illuminate\Foundation\Http\FormRequest;

class ProcessPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'string', 'max:100'],
            'transaction_reference' => ['nullable', 'string', 'max:255'],
            'paid_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required' => 'The payment amount is required.',
            'amount.numeric' => 'The payment amount must be a number.',
            'amount.min' => 'The payment amount must be at least 0.01.',
            'payment_method.required' => 'The payment method is required.',
            'payment_method.string' => 'The payment method must be a string.',
            'payment_method.max' => 'The payment method must not exceed 100 characters.',
            'transaction_reference.max' => 'The transaction reference must not exceed 255 characters.',
            'paid_date.date' => 'The paid date must be a valid date.',
        ];
    }
}
