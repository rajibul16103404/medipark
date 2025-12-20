<?php

namespace App\Models;

use App\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InstallmentRule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'payment_type',
        'regular_price',
        'special_discount',
        'offer_price',
        'down_payment_amount',
        'emi_amount',
        'duration_months',
        'waiver_frequency_months',
        'number_of_waivers',
        'waiver_amount_per_installment',
        'total_waiver_amount',
        'is_limited_time_offer',
        'status',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'regular_price' => 'decimal:2',
            'special_discount' => 'decimal:2',
            'offer_price' => 'decimal:2',
            'down_payment_amount' => 'decimal:2',
            'emi_amount' => 'decimal:2',
            'waiver_amount_per_installment' => 'decimal:2',
            'total_waiver_amount' => 'decimal:2',
            'is_limited_time_offer' => 'boolean',
            'status' => Status::class,
            'deleted_at' => 'datetime',
        ];
    }
}
