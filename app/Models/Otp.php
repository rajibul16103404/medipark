<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Otp extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'email',
        'otp',
        'type',
        'expires_at',
        'used',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'used' => 'boolean',
            'deleted_at' => 'datetime',
        ];
    }

    public function isValid(): bool
    {
        return ! $this->used && $this->expires_at->isFuture();
    }

    public function markAsUsed(): void
    {
        $this->update(['used' => true]);
    }
}
