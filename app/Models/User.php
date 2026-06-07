<?php

namespace App\Models;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
  /** @use HasFactory<UserFactory> */
  use HasFactory, Notifiable, HasApiTokens;

  protected $fillable = [
    'full_name',
    'user_name',
    'email',
    'password',
    'profile_picture',
    'role',
    'status',
    'suspended_at',
    'suspended_reason',
    'email_verified_at',
  ];

  protected $hidden = [
    'password',
    'remember_token',
  ];

  /**
   * Get the attributes that should be cast.
   *
   * @return array<string, string>
   */
  protected function casts(): array
  {
    return [
      'email_verified_at' => 'datetime',
      'password' => 'hashed',
    ];
  }

  public function bisnisOwner() {
    return $this->hasOne(BisnisOwner::class);
  }

  public function wishlists() {
    return $this->hasMany(Wishlist::class);
  }

  public function reviews() {
    return $this->hasMany(Review::class);
  }

  public function itineraries() {
    return $this->hasMany(Itinerary::class);
  }
}
