<?php

namespace App\Http\Resources;

use App\Traits\HasImageUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsResource extends JsonResource
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
            'description' => $this->description,
            'feature_image' => $this->getFullImageUrl($this->feature_image),
            'status' => $this->status?->value,
            'author_name' => $this->author_name,
            'author_image' => $this->getFullImageUrl($this->author_image),
            'author_designation' => $this->author_designation,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
