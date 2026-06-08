<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BisnisOwnerResource extends JsonResource
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
          'nik' => $this->nik,
          'ktp_photo' => $this->ktp_photo,
          'nib' => $this->nib,
          'verification_status' => $this->verification_status,
          'verification_notes' => $this->verification_notes,
          'user' => new UserResource($this->whenLoaded('user')),
          'destinations' => DestinationResource::collection($this->whenLoaded('destinations')),
        ];
    }
}
