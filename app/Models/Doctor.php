<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Doctor extends Model
{
    /** @use HasFactory<\Database\Factories\DoctorFactory> */
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'doctor_identity_number',
        'doctor_name',
        'department',
        'specialist',
        'facility_id',
        'email_address',
        'mobile_number',
        'gender',
        'date_of_birth',
        'known_languages',
        'education',
        'experience',
        'social_media',
        'membership',
        'awards',
        'registration_number',
        'about',
        'image',
        'present_address',
        'permanent_address',
    ];

    /**
     * Get the facility that this doctor belongs to.
     */
    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'known_languages' => 'array',
            'education' => 'array',
            'experience' => 'array',
            'social_media' => 'array',
            'membership' => 'array',
            'awards' => 'array',
            'deleted_at' => 'datetime',
        ];
    }
}
