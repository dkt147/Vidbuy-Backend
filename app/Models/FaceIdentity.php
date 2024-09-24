<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaceIdentity extends Model
{
    use HasFactory;

    protected $table = 'face_identities';

    protected $fillable =
    [
        'user_id',
        'link',
        'slug'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


}


