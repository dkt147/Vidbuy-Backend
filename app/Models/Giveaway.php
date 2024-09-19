<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Giveaway extends Model
{
    use HasFactory;

    protected $table = 'giveaways';

    protected $fillable =
    [
        'user_id',
        'title',
        'description',
        'price',
        'image',
        'like'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
