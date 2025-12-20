<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InstallmentRuleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'payment_type' => $this->payment_type,
            'regular_price' => $this->regular_price,
            'special_discount' => $this->special_discount,
            'offer_price' => $this->offer_price,
            'down_payment_amount' => $this->down_payment_amount,
            'emi_amount' => $this->emi_amount,
            'duration_months' => $this->duration_months,
            'waiver_frequency_months' => $this->waiver_frequency_months,
            'number_of_waivers' => $this->number_of_waivers,
            'waiver_amount_per_installment' => $this->waiver_amount_per_installment,
            'total_waiver_amount' => $this->total_waiver_amount,
            'is_limited_time_offer' => $this->is_limited_time_offer,
            'status' => $this->status,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
