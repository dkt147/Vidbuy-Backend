<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiveawayDoner extends Model
{
    use HasFactory;

    protected $table = 'giveaway_doners';

    protected $fillable =
    [
        'giveaway_id',
        'influencer_id',
        'user_id',
        'points'
    ];
}
