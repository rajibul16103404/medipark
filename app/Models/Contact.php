<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'patient_name',
        'gender',
        'phone_number',
        'email',
        'date_of_birth',
        'message',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'deleted_at' => 'datetime',
        ];
    }
}
