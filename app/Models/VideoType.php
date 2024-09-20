<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price'
    ];

    public function requestVideos()
    {
        return $this->hasMany(RequestVideo::class, 'video_type_id');
    }
}
