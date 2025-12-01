<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
    ];

    protected static function booted(): void
    {
        // Ensure admin role always has all privileges
        static::saved(function (Role $role) {
            if ($role->isAdmin()) {
                $allPrivileges = Privilege::pluck('id');
                $role->privileges()->sync($allPrivileges);
            }
        });
    }

    protected function casts(): array
    {
        return [
            'deleted_at' => 'datetime',
        ];
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_roles');
    }

    public function privileges(): BelongsToMany
    {
        return $this->belongsToMany(Privilege::class, 'privilege_role');
    }

    public function isAdmin(): bool
    {
        return $this->slug === 'admin';
    }
}
