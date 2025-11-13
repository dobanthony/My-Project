<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ShopFollower;
use App\Models\ShopRating; // ✅ Correct model
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductGuestController extends Controller
{
    public function show($id)
    {
        $product = Product::with([
            'shop:id,shop_name,shop_logo,shop_description,phone_number,email_address,created_at',
            'customization.customOptions.colors',
            'customization.customOptions.sizes',
            'customization.customOptions.patterns'
        ])->findOrFail($id);

        // ✅ Fetch ratings from ShopRating
        $ratings = ShopRating::where('product_id', $id)
            ->with('user:id,first_name,last_name,avatar')
            ->latest()
            ->get();

        // ✅ Fix average rating column name
        $averageRating = number_format($ratings->avg('product_rating') ?? 0, 1);
        $ratingsCount = $ratings->count();

        // ✅ Total sold
        $totalSold = $product->orders()
            ->where('status', 'approved')
            ->sum('quantity');

        // ✅ Followers count
        $followerCount = $product->shop->followers()->count();

        return Inertia::render('ProductGuest', [
            'product'        => $product,
            'ratings'        => $ratings,
            'averageRating'  => $averageRating,
            'ratingsCount'   => $ratingsCount,
            'totalSold'      => $totalSold,
            'isFollowing'    => false, // Guests can't follow
            'followerCount'  => $followerCount,
        ]);
    }
}
