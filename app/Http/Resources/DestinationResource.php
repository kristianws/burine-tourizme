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
        'description' => $this->description,
        'open_time' => $this->open_time ? $this->open_time->format('H:i:s') : null,
        'close_time' => $this->close_time ? $this->close_time->format('H:i:s') : null,
        'thumbnail' => $this->thumbnail,
        'images' => ImageResource::collection($this->whenLoaded('imageGaleries')),
        'category' => new CategoryResource($this->whenLoaded('category')),
        'bisnis_owner' => new BisnisOwnerResource($this->whenLoaded('bisnisOwner')),
        'reviews' => ReviewResource::collection($this->whenLoaded('reviews')),
      ];
    }
}
