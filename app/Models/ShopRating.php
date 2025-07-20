<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'shop_id',
        'product_id',
        'product_rating',
        'shop_rating',
        'comment',
        'image', // ✅ include this to allow mass assignment
    ];

    // 👇 Relationships (optional but useful)

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
