<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SellerController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();

        return inertia('Seller/SellerDash', [
            'user' => $user,
            'shop' => $user->shop, // can be null
        ]);
    }

    public function index()
    {
        $user = auth()->user();
        $shop = $user->shop;

        return Inertia::render('Seller/ShopEdit', [
            'user' => $user,
            'shop' => $shop,
        ]);
    }

    public function storeOrUpdate(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'shop_name' => 'required|string|max:255',
            'shop_description' => 'nullable|string',
            'shop_logo' => 'nullable|image|max:2048',
            'phone_number' => 'nullable|string|max:20',
            'email_address' => 'nullable|email|max:255',
        ]);

        $shop = $user->shop ?? new Shop(['user_id' => $user->id]);

        $shop->shop_name = $request->shop_name;
        $shop->shop_description = $request->shop_description;
        $shop->phone_number = $request->phone_number;
        $shop->email_address = $request->email_address;

        if ($request->hasFile('shop_logo')) {
            $shop->shop_logo = $request->file('shop_logo')->store('shop_logos', 'public');
        }

        $shop->save();

        return redirect()->route('seller.dashboard')->with('success', 'Shop profile saved.');
    }
}
