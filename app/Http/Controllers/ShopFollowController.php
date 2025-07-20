<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;

class ShopFollowController extends Controller
{
    public function toggle(Request $request, Shop $shop)
    {
        $user = $request->user();

        if ($user->followedShops()->where('shop_id', $shop->id)->exists()) {
            $user->followedShops()->detach($shop->id);
            return back()->with('message', 'Unfollowed the shop.');
        } else {
            $user->followedShops()->attach($shop->id);
            return back()->with('message', 'Followed the shop.');
        }
    }
}
