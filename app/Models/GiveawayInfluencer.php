<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiveawayInfluencer extends Model
{
    use HasFactory;

    protected $table = 'giveaway_influencers';

    protected $fillable =
    [
        'influencer_id',
        'giveaway_id',
        'total_points'
    ];
}
