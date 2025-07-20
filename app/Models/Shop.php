<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',    
        'shop_name',
        'shop_description',
        'shop_logo',
    ];
    // protected $appends = ['shop_rating'];
    protected $appends = ['shop_rating', 'shop_rating_count'];

    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // In Shop.php
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function followers()
{
    return $this->belongsToMany(User::class, 'shop_user_follows');
}

public function followedShops()
{
    return $this->belongsToMany(Shop::class, 'shop_user_follows');
}

public function ratings()
{
    return $this->hasMany(ShopRating::class);
}

public function getShopRatingAttribute()
{
    return round($this->ratings()->avg('shop_rating'), 1) ?? 0;
}
public function getShopRatingCountAttribute()
{
    return $this->ratings()->count();
}

//delete this

public function messages()
{
    return $this->hasMany(Message::class);
}

public function owner()
{
    return $this->belongsTo(User::class, 'user_id');
}


}
