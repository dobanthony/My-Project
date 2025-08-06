<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
        'status',
        'delivery_date',
        'full_name',
        'phone_number',
        'email',
        'delivery_address',
        'notes',
        'delivery_status',
        'customization_details', // 👈 make sure this is fillable
    ];

    protected $casts = [
        'customization_details' => 'array', // ✅ Cast to array
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function receivedOrder()
    {
        return $this->hasOne(ReceivedOrder::class);
    }

    public function deliveryInfo()
    {
        return $this->hasOne(\App\Models\DeliveryInfo::class, 'user_id', 'user_id');
    }
}
