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
      $supabaseUrl = 'https://upvdjamlioioilqhlytv.supabase.co/storage/v1/object/public/destination_images';  
      $bucketName = 'destination_images';
      return [
        'id' => $this->id,
        'destination_id' => $this->destination_id,
        'image_url' => $this->path ? $supabaseUrl . $bucketName . '/'. $this->destination_id . '/' . $this->path : null,
      ];
    }
}
