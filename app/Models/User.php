<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use HasFactory, Notifiable, SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'password',
        'avatar',
        'first_name',
        'middle_name',
        'last_name',
        'phone',
        'address',
        'dob',
        'application_reason', // ✅ add this
        'seller_status',       // ✅ add this
        'valid_id_type', 
        'valid_id_photos',
        'role',                // optional if you update 'role' somewhere
    ];
    protected $casts = [
    'social_links' => 'array',
    'valid_id_photos' => 'array',
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

    public function deliveryInfos()
    {
        return $this->hasMany(\App\Models\DeliveryInfo::class);
    }


}
