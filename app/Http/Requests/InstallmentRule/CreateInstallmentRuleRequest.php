<?php

namespace App\Http\Requests\InstallmentRule;

use App\Status;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateInstallmentRuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'payment_type' => ['required', 'string', 'in:full_payment,down_payment,emi_installment'],
            'regular_price' => ['required', 'numeric', 'min:0'],
            'special_discount' => ['nullable', 'numeric', 'min:0'],
            'offer_price' => ['required', 'numeric', 'min:0'],
            'down_payment_amount' => ['nullable', 'numeric', 'min:0', 'required_if:payment_type,down_payment'],
            'emi_amount' => ['nullable', 'numeric', 'min:0', 'required_if:payment_type,down_payment,emi_installment'],
            'duration_months' => ['nullable', 'integer', 'min:1', 'max:120', 'required_if:payment_type,down_payment,emi_installment'],
            'waiver_frequency_months' => ['nullable', 'integer', 'min:1'],
            'number_of_waivers' => ['nullable', 'integer', 'min:0'],
            'waiver_amount_per_installment' => ['nullable', 'numeric', 'min:0'],
            'total_waiver_amount' => ['nullable', 'numeric', 'min:0'],
            'is_limited_time_offer' => ['nullable', 'boolean'],
            'status' => ['nullable', Rule::enum(Status::class)],
            'description' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The name is required.',
            'name.string' => 'The name must be a string.',
            'name.min' => 'The name must be at least 2 characters.',
            'name.max' => 'The name must not exceed 255 characters.',
            'payment_type.required' => 'The payment type is required.',
            'payment_type.in' => 'The payment type must be one of: full_payment, down_payment, emi_installment.',
            'regular_price.required' => 'The regular price is required.',
            'regular_price.numeric' => 'The regular price must be a number.',
            'regular_price.min' => 'The regular price must be at least 0.',
            'offer_price.required' => 'The offer price is required.',
            'offer_price.numeric' => 'The offer price must be a number.',
            'offer_price.min' => 'The offer price must be at least 0.',
            'down_payment_amount.required_if' => 'The down payment amount is required when payment type is down_payment.',
            'down_payment_amount.numeric' => 'The down payment amount must be a number.',
            'emi_amount.required_if' => 'The EMI amount is required when payment type is down_payment or emi_installment.',
            'emi_amount.numeric' => 'The EMI amount must be a number.',
            'duration_months.required_if' => 'The duration in months is required when payment type is down_payment or emi_installment.',
            'duration_months.integer' => 'The duration must be an integer.',
            'duration_months.min' => 'The duration must be at least 1 month.',
            'duration_months.max' => 'The duration must not exceed 120 months.',
            'status.enum' => 'The status must be a valid status.',
        ];
    }
}
