<?php

namespace App\Http\Resources;

use App\Traits\HasImageUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    use HasImageUrl;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Ensure roles are loaded
        if (! $this->relationLoaded('roles')) {
            $this->load('roles');
        }

        return [
            'id' => $this->id,
            'identity_number' => $this->identity_number,
            'name' => $this->name,
            'email' => $this->email,
            'mobile_number' => $this->mobile_number,
            'gender' => $this->gender,
            'date_of_birth' => $this->date_of_birth?->format('Y-m-d'),
            'present_address' => $this->present_address,
            'permanent_address' => $this->permanent_address,
            'salary' => $this->salary ? (float) $this->salary : null,
            'blood_group' => $this->blood_group,
            'joining_date' => $this->joining_date?->format('Y-m-d'),
            'image' => $this->getFullImageUrl($this->image),
            'email_verified_at' => $this->email_verified_at?->toIso8601String(),
            'suspended_at' => $this->suspended_at?->toIso8601String(),
            'suspension_reason' => $this->suspension_reason,
            'is_suspended' => $this->isSuspended(),
            'roles' => $this->roles->map(fn ($role) => [
                'id' => $role->id,
                'name' => $role->name,
                'slug' => $role->slug,
            ]),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
