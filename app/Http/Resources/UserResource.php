<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
      $supabaseUrl = 'https://upvdjamlioioilqhlytv.supabase.co/storage/v1/object/public/';
      $bucketName = 'profile';
        return [
          'id' => $this->id,
          'fullname' => $this->fullname,
          'username' => $this->username,
          'email' => $this->email,
          'profile_picture_url' => $this->profile_picture ? $supabaseUrl . $bucketName . '/' . $this->profile_picture : $supabaseUrl . $bucketName . '/' . 'default_profile_picture.png',
          'role' => $this->role,
          'bisnis_owner' => new BisnisOwnerResource($this->whenLoaded('bisnisOwner')),
        ];
    }
}
