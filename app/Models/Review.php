<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable =
    [
        'user_id',
        'influencer_id',
        'message',
        'rating'
    ];

    public function reviewer() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
