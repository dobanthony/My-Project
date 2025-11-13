<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'shop_id',
        'name',
        'description',
        'price',
        'stock',
        'image',
        'eco_friendly',
        'category_id', 
    ];

    protected $casts = [
        'eco_friendly' => 'boolean',
    ];

    protected $dates = ['deleted_at'];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function customization()
    {
        return $this->hasOne(CustomizableProduct::class);
    }
    public function customizableProduct()
{
    return $this->hasOne(CustomizableProduct::class);
}

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function productRatings()
    {
        return $this->hasMany(\App\Models\ShopRating::class, 'product_id');
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

}
