<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'login_type',
        'role_id',
        'country_id',
        'country_name',
        'status',
        'firebase_token',
        'first_login',
        'socket_id',
        'image',
        'avg_rating',
        'review_count '
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function streams()
    {
        return $this->hasMany(Stream::class, 'user_id');
    }
    public function giveaways()
    {
        return $this->hasMany(Giveaway::class, 'user_id');
    }

    public function influencerCategories() {
        return $this->hasMany(InfluencerCategory::class);
    }

    public function reviews() {
        return $this->hasMany(Review::class, 'influencer_id');
    }
}
