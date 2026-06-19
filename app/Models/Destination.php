<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Destination extends Model
{
    //
    protected $fillable = [
        'bisnis_owner_id',
        'category_id',
        'name',
        'gmaps',
        'location',
        'price',
        'description',
        'open_time',
        'close_time',
        'thumbnail',
        'status',
        'notes',
        'deleted_at'
    ];

    protected $casts = [
        'price' => 'integer',
        'deleted_at' => 'datetime',
    ];

    public function bisnisOwner()
    {
        return $this->belongsTo(BisnisOwner::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function imageGaleries()
    {
        return $this->hasMany(ImageGalery::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function itineraryItems()
    {
        return $this->hasMany(ItineraryItem::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    protected function cast(): array
    {
        return [
            'deleted_at' => 'datetime',
        ];
    }
}
