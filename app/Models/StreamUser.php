<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StreamUser extends Model
{
    use HasFactory;
    protected $fillable =
    [
        'user_id',
        'stream_id',
        'joining_datetime',
        'exit_datetime',
    ];
}
