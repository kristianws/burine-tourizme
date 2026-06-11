<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ImageResource extends JsonResource
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
        'destination_id' => $this->destination_id,
        'image_url' => $this->path ? Storage::disk('supabase_images_galery')->url($this->path) : null,
      ];
    }
}
