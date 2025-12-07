<?php

namespace App\Models;

use App\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AboutUsPageBannerSection extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'background_image',
        'opacity',
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
     * Get the active about us page banner section.
     */
    public static function active(): ?self
    {
        return static::where('status', Status::Active->value)->first();
    }
}
