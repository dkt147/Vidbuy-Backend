<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InfluencerCategory extends Model
{
    use HasFactory;
    protected $table = 'influencer_categories';

    protected $fillable = [
        'user_id',
        'category_id',
    ];
}
