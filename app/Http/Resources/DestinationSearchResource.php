<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DestinationSearchResource extends JsonResource
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
          'thumbnail' => $this->thumbnail,
          'category' => $this->category->name,
          'rating' => (float) ($this->reviews_avg_rating ?? 0),
        ];
    }
}
