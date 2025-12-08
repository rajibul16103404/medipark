<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Investor extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'file_number',
        'applicant_full_name',
        'fathers_name',
        'mothers_name',
        'spouses_name',
        'present_address',
        'permanent_address',
        'nid_pp_bc_number',
        'tin_number',
        'date_of_birth',
        'nationality',
        'religion',
        'mobile_number',
        'email',
        'gender',
        'residency_status',
        'marital_status',
        'marriage_date',
        'organization',
        'profession',
        'applicant_image',
        'nominee_name',
        'nominee_relation',
        'nominee_mobile_number',
        'nominee_nid_pp_bc_number',
        'nominee_present_address',
        'nominee_permanent_address',
        'nominee_image',
        'project_name',
        'project_present_address',
        'project_permanent_address',
        'category_of_share',
        'price_per_hss',
        'number_of_hss',
        'total_price',
        'total_price_in_words',
        'special_discount',
        'installment_per_month',
        'mode_of_payment',
        'others_instructions',
        'agreed_price',
        'installment_start_from',
        'installment_start_to',
        'booking_money',
        'booking_money_in_words',
        'booking_money_date',
        'booking_money_cash_cheque_no',
        'booking_money_branch',
        'booking_money_on_or_before',
        'booking_money_mobile_number',
        'payment_in_words',
        'final_payment_date',
        'bank_name',
        'down_payment',
        'down_payment_date',
        'instructions_if_any',
        'reference_name_a',
        'reference_name_b',
        'rest_amount',
        'rest_amount_in_words',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'marriage_date' => 'date',
            'installment_start_from' => 'date',
            'installment_start_to' => 'date',
            'booking_money_date' => 'date',
            'booking_money_on_or_before' => 'date',
            'final_payment_date' => 'date',
            'down_payment_date' => 'date',
            'price_per_hss' => 'decimal:2',
            'total_price' => 'decimal:2',
            'special_discount' => 'decimal:2',
            'installment_per_month' => 'decimal:2',
            'agreed_price' => 'decimal:2',
            'booking_money' => 'decimal:2',
            'down_payment' => 'decimal:2',
            'rest_amount' => 'decimal:2',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     */
    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array<string, mixed>
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }
}
