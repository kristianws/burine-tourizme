<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Itinerary extends Model
{
  //
  use HasFactory;

  protected $fillable = [
    'user_id',
    'title',
    'start_date',
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function itineraryItems()
  {
    return $this->hasMany(ItineraryItem::class);
  }

  public function calculateAndUpdateEstimatedPrice(): void
  {
    $total = $this->itineraryItems()
      ->join('destinations', 'itinerary_items.destination_id', '=', 'destinations.id')
      ->sum('destinations.price');

    $this->update(['estimated_price' => $total]);
  }
}
