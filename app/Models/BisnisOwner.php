<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class BisnisOwner extends Model
{
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

    protected $hidden = [
      'nik',
      'ktp_photo',
      'nib'
    ];

    protected $casts = [
      'verification_at' => 'datetime',
    ];

    public function user() : BelongsTo
    {
      return $this->belongsTo(User::class);
    }

    public function destinations() : HasMany
    {
      return $this->hasMany(Destination::class);
    }
}
