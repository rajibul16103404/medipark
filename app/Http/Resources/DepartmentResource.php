<?php

namespace App\Http\Resources;

use App\Traits\HasImageUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentResource extends JsonResource
{
    use HasImageUrl;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Use loaded_doctors and loaded_blogs if available, otherwise use IDs
        $doctors = $this->loaded_doctors ?? $this->doctors ?? [];
        $blogs = $this->loaded_blogs ?? $this->blogs ?? [];

        // Transform doctors to use DoctorResource if loaded_doctors exists
        if (isset($this->loaded_doctors) && ! empty($this->loaded_doctors)) {
            $doctors = collect($this->loaded_doctors)->map(function ($doctor) {
                return [
                    'id' => $doctor['id'],
                    'doctor_name' => $doctor['doctor_name'] ?? null,
                    'department' => $doctor['department'] ?? null,
                    'specialist' => $doctor['specialist'] ?? null,
                    'image' => $this->getFullImageUrl($doctor['image'] ?? null),
                ];
            })->values()->toArray();
        }

        // Transform blogs to use BlogResource if loaded_blogs exists
        if (isset($this->loaded_blogs) && ! empty($this->loaded_blogs)) {
            $blogs = collect($this->loaded_blogs)->map(function ($blog) {
                return [
                    'id' => $blog['id'],
                    'title' => $blog['title'] ?? null,
                    'description' => $blog['description'] ?? null,
                    'feature_image' => $this->getFullImageUrl($blog['feature_image'] ?? null),
                    'status' => $blog['status'] ?? null,
                    'author_name' => $blog['author_name'] ?? null,
                    'author_image' => $this->getFullImageUrl($blog['author_image'] ?? null),
                    'author_designation' => $blog['author_designation'] ?? null,
                ];
            })->values()->toArray();
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'short_description' => $this->short_description,
            'description1' => $this->description1,
            'accordions' => $this->accordions ?? [],
            'description2' => $this->description2,
            'footer' => $this->footer,
            'image' => $this->getFullImageUrl($this->image),
            'status' => $this->status,
            'doctors' => $doctors,
            'blogs' => $blogs,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
