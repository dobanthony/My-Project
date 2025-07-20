<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = Cart::with('product')->where('user_id', auth()->id())->get();
        return inertia('Cart/Index', ['cartItems' => $cartItems]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $existingCart = Cart::where('user_id', auth()->id())
            ->where('product_id', $request->product_id)
            ->first();

        if ($existingCart) {
            //Overwrite existing quantity instead of incrementing
            $existingCart->quantity = $request->quantity;
            $existingCart->save();
        } else {
            //Create new cart item
            Cart::create([
                'user_id' => auth()->id(),
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
            ]);
        }

        return back()->with('message', 'Added to cart!');
    }

    public function update(Request $request, Cart $cart)
    {
        // $this->authorize('update', $cart);
        $request->validate(['quantity' => 'required|integer|min:1']);
        $cart->update(['quantity' => $request->quantity]);
        return back()->with('message', 'Cart updated!');
    }

    public function destroy(Cart $cart)
    {
        // $this->authorize('delete', $cart);
        $cart->delete();
        return back()->with('message', 'Item removed!');
    }
}
