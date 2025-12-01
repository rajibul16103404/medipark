<?php

namespace App\Models;

use App\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HomepageAboutUsSection extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'sub_title',
        'content',
        'image_1',
        'image_2',
        'image_3',
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
     * Get the active homepage about us section.
     */
    public static function active(): ?self
    {
        return static::where('status', Status::Active->value)->first();
    }
}
