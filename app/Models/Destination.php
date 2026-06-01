<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Destination extends Model
{
    protected $fillable = [
        'mitra_id',
        'category_id',
        'name',
        'description',
        'location',
        'business_license_number',
        'open_time',
        'close_time',
        'thumbnail',
        'status',
        'approved_at'
    ];
    public function mitra()
    {
        return $this->belongsTo(User::class, 'mitra_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(DestinationImage::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function itineraryItems()
    {
        return $this->hasMany(
            ItineraryItem::class
        );
    }

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',

            'open_time' => 'datetime:H:i',

            'close_time' => 'datetime:H:i',
        ];
    }
}
