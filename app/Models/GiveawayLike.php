<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiveawayLike extends Model
{
    use HasFactory;

    protected $table = 'giveaway_likes';

    protected $fillable =
    [
        'user_id',
        'giveaway_id'
    ];
}
