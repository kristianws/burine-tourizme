<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DestinationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
      return [
        'id' => $this->id,
        'name' => $this->name,
        'location' => $this->location,
        'price' => $this->price,
        'average_rating' => (float) $this->reviews_avg_rating ?? 0,
        'description' => $this->description,
        'open_time' => $this->open_time ? $this->open_time : null,
        'close_time' => $this->close_time ? $this->close_time : null,
        'thumbnail' => $this->thumbnail ? Storage::disk('supabase_thumbnail')->url($this->thumbnail) : null,
        'images' => ImageResource::collection($this->whenLoaded('imageGaleries')),
        'category' => new CategoryResource($this->whenLoaded('category')),
        'bisnis_owner' => new BisnisOwnerResource($this->whenLoaded('bisnisOwner')),
        'reviews' => ReviewResource::collection($this->whenLoaded('reviews')),
      ];
    }
}
