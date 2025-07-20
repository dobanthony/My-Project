<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ShopRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderRatingController extends Controller
{
    /**
     * Handle rating submission for shop and product.
     */
    public function rateShop(Request $request, Order $order)
    {
        //Validate request
        $request->validate([
            'shop_rating' => 'required|integer|min:1|max:5',
            'product_rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // max 2MB
        ]);

        //Prevent duplicate rating for the same order
        $existingRating = ShopRating::where('order_id', $order->id)->first();
        if ($existingRating) {
            return back()->withErrors(['message' => 'You have already rated this order.']);
        }

        //Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            // Store image in 'storage/app/public/ratings'
            $imagePath = $request->file('image')->store('ratings', 'public');
            
        }

        //Save rating with proper image path
        ShopRating::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'shop_id' => $order->product->shop->id ?? null,
            'product_id' => $order->product->id ?? null,
            'shop_rating' => $request->shop_rating,
            'product_rating' => $request->product_rating,
            'comment' => $request->comment,
            'image' => $imagePath, // e.g., 'ratings/filename.jpg'
        ]);

        return back()->with('success', '✅ Thank you for rating this order!');
    }
}
