<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvestorInstallmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $paidAmount = (float) ($this->paid_amount ?? 0);
        $totalAmount = (float) $this->amount;
        $dueAmount = max(0, $totalAmount - $paidAmount);

        return [
            'id' => $this->id,
            'investor_id' => $this->investor_id,
            'investor' => $this->whenLoaded('investor', fn () => new InvestorResource($this->investor)),
            'installment_number' => $this->installment_number,
            'total_amount' => $totalAmount,
            'paid_amount' => $paidAmount,
            'due_amount' => $dueAmount,
            'amount' => $totalAmount, // Keep for backward compatibility
            'due_date' => $this->due_date?->toDateString(),
            'paid_date' => $this->paid_date?->toDateString(),
            'status' => $this->status?->value,
            'payment_method' => $this->payment_method,
            'transaction_reference' => $this->transaction_reference,
            'notes' => $this->notes,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
