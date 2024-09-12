<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    use HasFactory;
    protected $table = 'user_profiles';
    protected $fillable =
    [
        'user_id',
        'language_id',
        'push_notification',
        'email_notification'
    ];
}
