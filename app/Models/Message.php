<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'sender_id',
        'receiver_id',
        'message',
        'is_read',
        'product_id',
    ];

    // 🔗 Relationships

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
    public function product()
    {
        return $this->belongsTo(\App\Models\Product::class);
    }

}
