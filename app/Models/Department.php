<?php

namespace App\Models;

use App\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    /** @use HasFactory<\Database\Factories\DepartmentFactory> */
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'short_description',
        'description1',
        'accordions',
        'description2',
        'footer',
        'image',
        'status',
        'is_specialized',
        'doctors',
        'blogs',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'accordions' => 'array',
            'doctors' => 'array',
            'blogs' => 'array',
            'is_specialized' => 'boolean',
            'status' => Status::class,
            'deleted_at' => 'datetime',
        ];
    }
}
