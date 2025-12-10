<?php

namespace App\Http\Resources;

use App\Traits\HasImageUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GalleryResource extends JsonResource
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
            'title' => $this->title,
            'date' => $this->date?->toDateString(),
            'image' => $this->getFullImageUrl($this->image),
            'status' => $this->status?->value,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
