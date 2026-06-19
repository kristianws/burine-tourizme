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
        $supabaseUrl = config('services.supabase.url') . '/storage/v1/object/public/';
        $bucketName = 'thumbnail';
        return [
            'id' => $this->id,
            'name' => $this->name,
            'gmaps' => $this->gmaps,
            'location' => $this->location,
            'price' => $this->price,
            'category_id' => $this->category_id,
            'status' => $this->status,
            'average_rating' => (float) $this->reviews_avg_rating ?? 0,
            'description' => $this->description,
            'open_time' => $this->open_time ? substr((string) $this->open_time, 0, 5) : null,
            'close_time' => $this->close_time ? substr((string) $this->close_time, 0, 5) : null,
            'thumbnail' => $this->thumbnail ? $supabaseUrl . $bucketName . '/' . $this->thumbnail : null,
            'images' => ImageResource::collection($this->whenLoaded('imageGaleries')),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'bisnis_owner' => new BisnisOwnerResource($this->whenLoaded('bisnisOwner')),
            'reviews' => ReviewResource::collection($this->whenLoaded('reviews')),
        ];
    }
}
