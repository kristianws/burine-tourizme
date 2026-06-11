<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WishlistResource extends JsonResource
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
            'user' => [
                'id' => $this->user_id,
                'username' => $this->user->username,
                'email' => $this->user->email,
            ],
            'destination' => [
                'id' => $this->destination_id,
                'name' => $this->destination->name,
                'location' => $this->destination->location,
                'price' => $this->destination->price,
                'thumbnail' => $this->destination->thumbnail,
                // Mengambil nilai rata-rata dari query withAvg()
                'rating' => $this->destination->reviews_avg_rating ?? 0, 
            ],
      ];
    }
}
