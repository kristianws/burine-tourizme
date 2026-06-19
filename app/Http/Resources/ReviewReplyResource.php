<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewReplyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'review_id' => $this->review_id,
            'user_id' => $this->user_id,
            'parent_id' => $this->parent_id,
            'content' => $this->content,
            'username' => $this->user?->username ?? 'User Dihapus',
            'fullname' => $this->user?->fullname ?? ($this->user?->username ?? 'User Dihapus'),
            'profile_picture' => $this->user?->profile_picture,
            'created_at' => $this->created_at,
            'children' => ReviewReplyResource::collection($this->whenLoaded('children')),
        ];
    }
}
