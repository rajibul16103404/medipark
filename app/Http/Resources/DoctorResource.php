<?php

namespace App\Http\Resources;

use App\Traits\HasImageUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorResource extends JsonResource
{
    use HasImageUrl;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'doctor_identity_number' => $this->doctor_identity_number,
            'doctor_name' => $this->doctor_name,
            'department' => $this->department,
            'specialist' => $this->specialist,
            'facility_id' => $this->facility_id,
            'facility' => $this->whenLoaded('facility', function () {
                return [
                    'id' => $this->facility->id,
                    'title' => $this->facility->title,
                    'short_description' => $this->facility->short_description,
                    'image' => $this->getFullImageUrl($this->facility->image),
                    'status' => $this->facility->status?->value,
                ];
            }),
            'email_address' => $this->email_address,
            'mobile_number' => $this->mobile_number,
            'gender' => $this->gender,
            'date_of_birth' => $this->date_of_birth?->format('Y-m-d'),
            'known_languages' => $this->known_languages,
            'education' => $this->education,
            'experience' => $this->experience,
            'social_media' => $this->social_media,
            'membership' => $this->membership,
            'awards' => $this->awards,
            'registration_number' => $this->registration_number,
            'about' => $this->about,
            'image' => $this->getFullImageUrl($this->image),
            'present_address' => $this->present_address,
            'permanent_address' => $this->permanent_address,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
