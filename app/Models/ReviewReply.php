<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewReply extends Model
{
    protected $fillable = [
        'review_id',
        'user_id',
        'parent_id',
        'content',
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
        return $this->belongsTo(ReviewReply::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ReviewReply::class, 'parent_id');
    }
}
