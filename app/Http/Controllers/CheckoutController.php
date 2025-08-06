<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\DeliveryInfo;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    public function show(Request $request, Product $product)
    {
        $quantity = (int) $request->input('quantity', 1);

        // ✅ Get latest delivery info for the user
        $deliveryInfo = auth()->user()->deliveryInfos()->latest()->first();

        return Inertia::render('Checkout/Form', [
            'product' => $product,
            'quantity' => $quantity,
            'lastDeliveryInfo' => $deliveryInfo, // 🧠 Pass as prop
        ]);
    }

    public function create(Product $product)
    {
        $lastInfo = auth()->user()->deliveryInfos()->latest()->first();

        return Inertia::render('Checkout/Form', [
            'product' => $product,
            'quantity' => request('quantity', 1),
            'lastDeliveryInfo' => $lastInfo,
        ]);
    }

    public function bulkForm(Request $request)
    {
        $cartItems = collect($request->input('items', []));
        $lastInfo = auth()->user()->deliveryInfos()->latest()->first();

        return Inertia::render('Checkout/BulkForm', [
            'cartItems' => $cartItems,
            'lastDeliveryInfo' => $lastInfo,
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
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->stock < $request->quantity) {
            return back()->withErrors(['quantity' => 'Not enough stock available.']);
        }

        // ✅ Update or create one delivery info per user
        DeliveryInfo::updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'full_name' => $request->full_name,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'delivery_address' => $request->delivery_address,
                'notes' => $request->notes,
            ]
        );

        // ✅ Create the order
        Order::create([
            'user_id' => auth()->id(),
            'product_id' => $product->id,
            'full_name' => $request->full_name,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'delivery_address' => $request->delivery_address,
            'notes' => $request->notes,
            'quantity' => $request->quantity,
            'status' => 'pending',
        ]);

        $product->decrement('stock', $request->quantity);
        $product->increment('total_sold', $request->quantity);

        return redirect()->route('my-orders')->with('success', 'Order placed. Waiting for approval.');
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

        // Update or create delivery info (1 per user)
        DeliveryInfo::updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'full_name' => $request->full_name,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'delivery_address' => $request->delivery_address,
                'notes' => $request->notes,
            ]
        );

        foreach ($request->orders as $order) {
            $product = Product::findOrFail($order['product_id']);

            if ($product->stock < $order['quantity']) {
                return back()->withErrors([
                    'orders' => "Not enough stock for product: {$product->name}"
                ]);
            }

            Order::create([
                'user_id' => auth()->id(),
                'product_id' => $product->id,
                'quantity' => $order['quantity'],
                'status' => 'pending',
                'full_name' => $request->full_name,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'delivery_address' => $request->delivery_address,
                'notes' => $request->notes,
            ]);

            $product->decrement('stock', $order['quantity']);
            $product->increment('total_sold', $order['quantity']);
        }

        return redirect()->route('my-orders')->with('success', 'Bulk order placed successfully!');
    }
}
