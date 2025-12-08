<?php

namespace App\Http\Requests\Investor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInvestorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file_number' => ['sometimes', 'required', 'string', 'max:100'],
            'applicant_full_name' => ['sometimes', 'required', 'string', 'max:255'],
            'fathers_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'mothers_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'spouses_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'present_address' => ['sometimes', 'nullable', 'string'],
            'permanent_address' => ['sometimes', 'nullable', 'string'],
            'nid_pp_bc_number' => ['sometimes', 'nullable', 'string', 'max:100'],
            'tin_number' => ['sometimes', 'nullable', 'string', 'max:100'],
            'date_of_birth' => ['sometimes', 'nullable', 'date'],
            'nationality' => ['sometimes', 'nullable', 'string', 'max:100'],
            'religion' => ['sometimes', 'nullable', 'string', 'max:100'],
            'mobile_number' => ['sometimes', 'required', 'string', 'max:50'],
            'email' => ['sometimes', 'nullable', 'email', 'max:255'],
            'gender' => ['sometimes', 'nullable', Rule::in(['male', 'female', 'other'])],
            'residency_status' => ['sometimes', 'nullable', Rule::in(['resident', 'non_resident'])],
            'marital_status' => ['sometimes', 'nullable', Rule::in(['single', 'married', 'divorced', 'widowed', 'other'])],
            'marriage_date' => ['sometimes', 'nullable', 'date'],
            'organization' => ['sometimes', 'nullable', 'string', 'max:255'],
            'profession' => ['sometimes', 'nullable', 'string', 'max:255'],
            'applicant_image' => $this->imageOrUrlRule(),

            'nominee_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'nominee_relation' => ['sometimes', 'nullable', 'string', 'max:255'],
            'nominee_mobile_number' => ['sometimes', 'nullable', 'string', 'max:50'],
            'nominee_nid_pp_bc_number' => ['sometimes', 'nullable', 'string', 'max:100'],
            'nominee_present_address' => ['sometimes', 'nullable', 'string'],
            'nominee_permanent_address' => ['sometimes', 'nullable', 'string'],
            'nominee_image' => $this->imageOrUrlRule(),

            'project_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'project_present_address' => ['sometimes', 'nullable', 'string'],
            'project_permanent_address' => ['sometimes', 'nullable', 'string'],
            'category_of_share' => ['sometimes', 'nullable', 'string', 'max:100'],
            'price_per_hss' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'number_of_hss' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'total_price' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'total_price_in_words' => ['sometimes', 'nullable', 'string', 'max:255'],
            'special_discount' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'installment_per_month' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'mode_of_payment' => ['sometimes', 'nullable', 'string', 'max:255'],
            'others_instructions' => ['sometimes', 'nullable', 'string'],
            'agreed_price' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'installment_start_from' => ['sometimes', 'nullable', 'date'],
            'installment_start_to' => ['sometimes', 'nullable', 'date'],

            'booking_money' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'booking_money_in_words' => ['sometimes', 'nullable', 'string', 'max:255'],
            'booking_money_date' => ['sometimes', 'nullable', 'date'],
            'booking_money_cash_cheque_no' => ['sometimes', 'nullable', 'string', 'max:255'],
            'booking_money_branch' => ['sometimes', 'nullable', 'string', 'max:255'],
            'booking_money_on_or_before' => ['sometimes', 'nullable', 'date'],
            'booking_money_mobile_number' => ['sometimes', 'nullable', 'string', 'max:50'],

            'payment_in_words' => ['sometimes', 'nullable', 'string', 'max:255'],
            'final_payment_date' => ['sometimes', 'nullable', 'date'],
            'bank_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'down_payment' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'down_payment_date' => ['sometimes', 'nullable', 'date'],
            'instructions_if_any' => ['sometimes', 'nullable', 'string'],
            'reference_name_a' => ['sometimes', 'nullable', 'string', 'max:255'],
            'reference_name_b' => ['sometimes', 'nullable', 'string', 'max:255'],
            'rest_amount' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'rest_amount_in_words' => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }

    private function imageOrUrlRule(): array
    {
        return [
            'sometimes',
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
