<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InfluencerVideoType extends Model
{
    use HasFactory;
    protected $table = 'influencer_video_types';

    protected $fillable = [
        'user_id',
        'video_type_id',
        'video_type_name',
        'price'
    ];
}
