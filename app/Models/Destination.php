<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Destination extends Model
{
    protected $table = 'destinations';

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'location',
        'google_maps_url',
        'image_url',
        'status'
    ];

    public function users() : BelongsToMany
    {
      return $this->belongsToMany(
        User::class,
        'wishlists',
        'destination_id',
        'user_id'
      )->withTimestamps();
    }

    

}
