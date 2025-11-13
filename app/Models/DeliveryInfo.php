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
        'province_id',
        'municipality_id',
        'barangay_id',
        'street_address',
        'notes',
    ];

    /**
     * The user who owns this delivery info
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The province associated with this delivery info
     */
    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    /**
     * The municipality associated with this delivery info
     */
    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }

    /**
     * The barangay associated with this delivery info
     */
    public function barangay()
    {
        return $this->belongsTo(Barangay::class);
    }
}
