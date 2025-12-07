<?php

namespace App\Models;

use App\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AboutUsPage2ndAfterOurVisionSection extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'about_us_page_2nd_after_our_vision_sections';

    protected $fillable = [
        'title',
        'paragraph',
        'image',
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
     * Get the active about us page 2nd after our vision section.
     */
    public static function active(): ?self
    {
        return static::where('status', Status::Active->value)->first();
    }
}
