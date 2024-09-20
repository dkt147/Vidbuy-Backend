<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestVideo extends Model
{
    use HasFactory;

    protected $fillable =
    [
        'user_id',
        'influencer_id',
        'video_for',
        'video_type_id',
        'from',
        'to',
        'description',
        'required_days',
        'delivery_charges',
        'service_charges',
        'total_price'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function videoType()
    {
        return $this->belongsTo(VideoType::class, 'video_type_id');
    }
}
