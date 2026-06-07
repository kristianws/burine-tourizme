<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItineraryItem extends Model
{
    //

    protected $fillable = [
      'itinerary_id',
      'destination_id',
      'day',
      'sequence_order',
      'start_time',
      'end_time',
    ];

    public function itinerary() {
      return $this->belongsTo(Itinerary::class);
    }

    public function destination() {
      return $this->belongsTo(Destination::class);
    }
}
