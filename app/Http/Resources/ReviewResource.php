<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
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
          'username' => $this->user->username,
          'profile_picture' => $this->user->profile_picture,
          'rating' => $this->rating,
          'description' => $this->description,
        ];
    }
}
