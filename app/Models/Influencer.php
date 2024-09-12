<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Influencer extends Model
{
    use HasFactory;

    protected $table = 'influencer_profiles';
    protected $fillable =
    [
        'user_id',
        'language_id',
        'push_notification',
        'email_notification'
    ];
}
