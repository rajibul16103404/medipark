<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    use HasFactory;

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
