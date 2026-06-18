<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImageGalery extends Model
{
    protected $fillable = [
        'destination_id',
        'path'
    ];

    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }
}
