<?php

namespace App\Http\Resources;

use App\Traits\HasImageUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvestorResource extends JsonResource
{
    use HasImageUrl;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'file_number' => $this->file_number,
            'applicant_full_name' => $this->applicant_full_name,
            'fathers_name' => $this->fathers_name,
            'mothers_name' => $this->mothers_name,
            'spouses_name' => $this->spouses_name,
            'present_address' => $this->present_address,
            'permanent_address' => $this->permanent_address,
            'nid_pp_bc_number' => $this->nid_pp_bc_number,
            'tin_number' => $this->tin_number,
            'date_of_birth' => $this->date_of_birth?->toDateString(),
            'nationality' => $this->nationality,
            'religion' => $this->religion,
            'mobile_number' => $this->mobile_number,
            'email' => $this->email,
            'gender' => $this->gender,
            'residency_status' => $this->residency_status,
            'marital_status' => $this->marital_status,
            'marriage_date' => $this->marriage_date?->toDateString(),
            'organization' => $this->organization,
            'profession' => $this->profession,
            'applicant_image' => $this->getFullImageUrl($this->applicant_image),

            'nominee_name' => $this->nominee_name,
            'nominee_relation' => $this->nominee_relation,
            'nominee_mobile_number' => $this->nominee_mobile_number,
            'nominee_nid_pp_bc_number' => $this->nominee_nid_pp_bc_number,
            'nominee_present_address' => $this->nominee_present_address,
            'nominee_permanent_address' => $this->nominee_permanent_address,
            'nominee_image' => $this->getFullImageUrl($this->nominee_image),

            'project_name' => $this->project_name,
            'project_present_address' => $this->project_present_address,
            'project_permanent_address' => $this->project_permanent_address,
            'category_of_share' => $this->category_of_share,
            'price_per_hss' => $this->price_per_hss,
            'number_of_hss' => $this->number_of_hss,
            'total_price' => $this->total_price,
            'total_price_in_words' => $this->total_price_in_words,
            'special_discount' => $this->special_discount,
            'installment_per_month' => $this->installment_per_month,
            'mode_of_payment' => $this->mode_of_payment,
            'others_instructions' => $this->others_instructions,
            'agreed_price' => $this->agreed_price,
            'installment_start_from' => $this->installment_start_from?->toDateString(),
            'installment_start_to' => $this->installment_start_to?->toDateString(),

            'booking_money' => $this->booking_money,
            'booking_money_in_words' => $this->booking_money_in_words,
            'booking_money_date' => $this->booking_money_date?->toDateString(),
            'booking_money_cash_cheque_no' => $this->booking_money_cash_cheque_no,
            'booking_money_branch' => $this->booking_money_branch,
            'booking_money_on_or_before' => $this->booking_money_on_or_before?->toDateString(),
            'booking_money_mobile_number' => $this->booking_money_mobile_number,

            'payment_in_words' => $this->payment_in_words,
            'final_payment_date' => $this->final_payment_date?->toDateString(),
            'bank_name' => $this->bank_name,
            'down_payment' => $this->down_payment,
            'down_payment_date' => $this->down_payment_date?->toDateString(),
            'instructions_if_any' => $this->instructions_if_any,
            'reference_name_a' => $this->reference_name_a,
            'reference_name_b' => $this->reference_name_b,
            'rest_amount' => $this->rest_amount,
            'rest_amount_in_words' => $this->rest_amount_in_words,

            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
