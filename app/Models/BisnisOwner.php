<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BisnisOwner extends Model
{
    //
    protected $fillable = [
      'user_id',
      'nik',
      'ktp_photo',
      'status',
      'verification_status',
      'verification_at',
      'verification_notes',
      'nib'
    ];
}
