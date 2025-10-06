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
        'allow_size',
        'allow_material',
        'allow_pattern',
        'custom_options', // JSON column to store nested options
    ];

    protected $casts = [
        'custom_options' => 'array', // Automatically cast JSON to array
    ];

    /**
     * Relationship to the main product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
