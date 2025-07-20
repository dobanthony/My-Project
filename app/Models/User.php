<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'first_name',
        'middle_name',
        'last_name',
        'phone',
        'address',
        'dob',
        'social_links',
        'application_reason', // ✅ add this
        'seller_status',       // ✅ add this
        'role',                // optional if you update 'role' somewhere
    ];
    protected $casts = [
    'social_links' => 'array',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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

    public function shop()
    {
        return $this->hasOne(Shop::class);
    }

    public function followedShops()
    {
        return $this->belongsToMany(Shop::class, 'shop_user_follows');
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'shop_user_follows');
    }


    //delte this
    public function sentMessages()
{
    return $this->hasMany(Message::class, 'sender_id');
}

public function receivedMessages()
{
    return $this->hasMany(Message::class, 'receiver_id');
}

public function latestMessage()
{
    return $this->hasMany(\App\Models\Message::class, 'sender_id')->latest();
}


}
