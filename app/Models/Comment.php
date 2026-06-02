<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'review_id',
        'user_id',
        'parent_id',
        'description'
    ];

    public function review()
    {
        return $this->belongsTo(Review::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(
            Comment::class,
            'parent_id'
        );
    }

    public function replies()
    {
        return $this->hasMany(
            Comment::class,
            'parent_id'
        );
    }
}