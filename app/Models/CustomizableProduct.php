<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomizableProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'allow_color',
        'allow_name',
        'allow_size',
        'allow_material',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
