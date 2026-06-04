<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BisnisOwner extends Model
{
    protected $fillable = [
      'user_id', 
      'nik',
      'ktp_image',
      'is_verified',
    ];

    public function user()
    {
      return $this->belongsTo(User::class);
    }


}
