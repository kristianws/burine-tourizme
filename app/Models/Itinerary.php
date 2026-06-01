<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Itinerary extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'travel_date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(
            ItineraryItem::class
        )
        ->orderBy('visit_order');
    }

    protected function casts(): array
    {
        return [
            'travel_date' => 'date'
        ];
    }
}