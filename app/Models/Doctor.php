<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
        'email_address',
        'mobile_number',
        'gender',
        'date_of_birth',
        'known_languages',
        'registration_number',
        'about',
        'image',
        'present_address',
        'permanent_address',
        'display_name',
        'user_name',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
    ];

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
            'password' => 'hashed',
            'deleted_at' => 'datetime',
        ];
    }
}
