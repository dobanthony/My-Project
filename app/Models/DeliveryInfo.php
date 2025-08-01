<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'full_name',
        'phone_number',
        'email',
        'delivery_address',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
