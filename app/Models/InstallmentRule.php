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
        'duration_months',
        'status',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'status' => Status::class,
            'deleted_at' => 'datetime',
        ];
    }
}
