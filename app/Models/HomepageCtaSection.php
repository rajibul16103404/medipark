<?php

namespace App\Models;

use App\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HomepageCtaSection extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'sub_title',
        'content',
        'button_text',
        'button_link',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => Status::class,
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Get the active homepage CTA section.
     */
    public static function active(): ?self
    {
        return static::where('status', Status::Active->value)->first();
    }
}
