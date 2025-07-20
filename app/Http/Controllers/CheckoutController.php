<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    public function show(Request $request, Product $product)
    {
        $quantity = (int) $request->input('quantity', 1); //it's a number

        return Inertia::render('Checkout/Form', [
            'product' => $product,
            'quantity' => $quantity
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'full_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email' => 'required|email',
            'delivery_address' => 'required|string',
            'notes' => 'nullable|string',
            'quantity' => 'required|integer|min:1'
        ]);

        Order::create([
            'user_id' => auth()->id(),
            'product_id' => $request->product_id,
            'full_name' => $request->full_name,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'delivery_address' => $request->delivery_address,
            'notes' => $request->notes,
            'quantity' => $request->quantity,
            'status' => 'pending'
        ]);

        return redirect()->route('my-orders')->with('success', 'Order placed. Waiting for approval.');
    }

    public function bulkForm(Request $request)
    {
        $cartItems = collect($request->input('items', []));

        return Inertia::render('Checkout/BulkForm', [
            'cartItems' => $cartItems
        ]);
    }

    public function bulkStore(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email' => 'required|email',
            'delivery_address' => 'required|string',
            'notes' => 'nullable|string',
            'orders' => 'required|array',
            'orders.*.product_id' => 'required|exists:products,id',
            'orders.*.quantity' => 'required|integer|min:1'
        ]);

        foreach ($request->orders as $order) {
            Order::create([
                'user_id' => auth()->id(),
                'product_id' => $order['product_id'],
                'quantity' => $order['quantity'],
                'status' => 'pending',
                'full_name' => $request->full_name,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'delivery_address' => $request->delivery_address,
                'notes' => $request->notes,
            ]);
        }

        return redirect()->route('my-orders')->with('success', 'Bulk order placed successfully!');
    }
}
