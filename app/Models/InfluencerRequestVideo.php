<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InfluencerRequestVideo extends Model
{
    use HasFactory;

    protected $table = 'influencer_request_videos';

    protected $fillable =
    [
        'user_id',
        'request_video_id',
        'link',
        'slug'
    ];
}
