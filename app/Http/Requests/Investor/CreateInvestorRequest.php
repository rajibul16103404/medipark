<?php

namespace App\Http\Requests\Investor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateInvestorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file_number' => ['required', 'string', 'max:100'],
            'applicant_full_name' => ['required', 'string', 'max:255'],
            'fathers_name' => ['nullable', 'string', 'max:255'],
            'mothers_name' => ['nullable', 'string', 'max:255'],
            'spouses_name' => ['nullable', 'string', 'max:255'],
            'present_address' => ['nullable', 'string'],
            'permanent_address' => ['nullable', 'string'],
            'nid_pp_bc_number' => ['nullable', 'string', 'max:100'],
            'tin_number' => ['nullable', 'string', 'max:100'],
            'date_of_birth' => ['nullable', 'date'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'religion' => ['nullable', 'string', 'max:100'],
            'mobile_number' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
            'residency_status' => ['nullable', Rule::in(['resident', 'non_resident'])],
            'marital_status' => ['nullable', Rule::in(['single', 'married', 'divorced', 'widowed', 'other'])],
            'marriage_date' => ['nullable', 'date'],
            'organization' => ['nullable', 'string', 'max:255'],
            'profession' => ['nullable', 'string', 'max:255'],
            'applicant_image' => $this->imageOrUrlRule(),

            'nominee_name' => ['nullable', 'string', 'max:255'],
            'nominee_relation' => ['nullable', 'string', 'max:255'],
            'nominee_mobile_number' => ['nullable', 'string', 'max:50'],
            'nominee_nid_pp_bc_number' => ['nullable', 'string', 'max:100'],
            'nominee_present_address' => ['nullable', 'string'],
            'nominee_permanent_address' => ['nullable', 'string'],
            'nominee_image' => $this->imageOrUrlRule(),

            'project_name' => ['nullable', 'string', 'max:255'],
            'project_present_address' => ['nullable', 'string'],
            'project_permanent_address' => ['nullable', 'string'],
            'category_of_share' => ['nullable', 'string', 'max:100'],
            'price_per_hss' => ['nullable', 'numeric', 'min:0'],
            'number_of_hss' => ['nullable', 'integer', 'min:0'],
            'total_price' => ['nullable', 'numeric', 'min:0'],
            'total_price_in_words' => ['nullable', 'string', 'max:255'],
            'special_discount' => ['nullable', 'numeric', 'min:0'],
            'installment_per_month' => ['nullable', 'numeric', 'min:0'],
            'mode_of_payment' => ['nullable', 'string', 'max:255'],
            'others_instructions' => ['nullable', 'string'],
            'agreed_price' => ['nullable', 'numeric', 'min:0'],
            'installment_start_from' => ['nullable', 'date'],
            'installment_start_to' => ['nullable', 'date'],

            'booking_money' => ['nullable', 'numeric', 'min:0'],
            'booking_money_in_words' => ['nullable', 'string', 'max:255'],
            'booking_money_date' => ['nullable', 'date'],
            'booking_money_cash_cheque_no' => ['nullable', 'string', 'max:255'],
            'booking_money_branch' => ['nullable', 'string', 'max:255'],
            'booking_money_on_or_before' => ['nullable', 'date'],
            'booking_money_mobile_number' => ['nullable', 'string', 'max:50'],

            'payment_in_words' => ['nullable', 'string', 'max:255'],
            'final_payment_date' => ['nullable', 'date'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'down_payment' => ['nullable', 'numeric', 'min:0'],
            'down_payment_date' => ['nullable', 'date'],
            'instructions_if_any' => ['nullable', 'string'],
            'reference_name_a' => ['nullable', 'string', 'max:255'],
            'reference_name_b' => ['nullable', 'string', 'max:255'],
            'rest_amount' => ['nullable', 'numeric', 'min:0'],
            'rest_amount_in_words' => ['nullable', 'string', 'max:255'],
        ];
    }

    private function imageOrUrlRule(): array
    {
        return [
            'nullable',
            function (string $attribute, mixed $value, $fail): void {
                if (is_string($value)) {
                    if (strlen($value) > 500) {
                        $fail('The '.$attribute.' must not exceed 500 characters.');
                    }

                    return;
                }

                if (! $this->hasFile($attribute)) {
                    return;
                }

                $file = $this->file($attribute);

                if (! $file->isValid()) {
                    $fail('The '.$attribute.' failed to upload.');

                    return;
                }

                $allowedImageTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];

                if (! in_array($file->getMimeType(), $allowedImageTypes, true)) {
                    $fail('The '.$attribute.' must be a valid image file (JPEG, PNG, GIF, WebP, or SVG).');
                }

                if ($file->getSize() > 20971520) {
                    $fail('The '.$attribute.' must not be larger than 20MB.');
                }
            },
        ];
    }
}
