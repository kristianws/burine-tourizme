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
          'user_id' => $this->user_id,
          'username' => $this->user->username,
          'fullname' => $this->user->fullname ?? $this->user->username,
          'profile_picture' => $this->user->profile_picture,
          'rating' => $this->rating,
          'description' => $this->description,
          'owner_reply' => $this->owner_reply,
          'created_at' => $this->created_at,
          'replies' => ReviewReplyResource::collection($this->whenLoaded('replies')),
        ];
    }
}
