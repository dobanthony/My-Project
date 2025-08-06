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
        $lastDeliveryInfo = auth()->user()->deliveryInfos()->latest()->first();

        // Extract customization from query
        $customizationDefaults = [
            'color' => $request->input('color'),
            'size' => $request->input('size'),
            'material' => $request->input('material'),
            'custom_name' => $request->input('custom_name'),
            'custom_description' => $request->input('custom_description'),
        ];

        return Inertia::render('Checkout/Form', [
            'product' => $product,
            'quantity' => $quantity,
            'lastDeliveryInfo' => $lastDeliveryInfo,
            'customizations' => $customizationDefaults,
        ]);
    }

    public function create(Product $product)
    {
        $product->load('customization');

        $lastDeliveryInfo = auth()->user()->deliveryInfos()->latest()->first();

        return Inertia::render('Checkout/Form', [
            'product' => $product,
            'quantity' => request('quantity', 1),
            'lastDeliveryInfo' => $lastDeliveryInfo,
            'customizations' => [
                'color' => null,
                'size' => null,
                'material' => null,
                'custom_name' => null,
                'custom_description' => null,
            ],
        ]);
    }

    public function bulkForm(Request $request)
    {
        $cartItems = collect($request->input('items', []));
        $lastDeliveryInfo = auth()->user()->deliveryInfos()->latest()->first();

        return Inertia::render('Checkout/BulkForm', [
            'cartItems' => $cartItems,
            'lastDeliveryInfo' => $lastDeliveryInfo,
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
            'color' => 'nullable|string|max:100',
            'size' => 'nullable|string|max:100',
            'material' => 'nullable|string|max:100',
            'custom_name' => 'nullable|string|max:100',
            'custom_description' => 'nullable|string|max:255',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->stock < $request->quantity) {
            return back()->withErrors(['quantity' => 'Not enough stock available.']);
        }

        // Save or update delivery info
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

        // Save the order
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
            'customization_details' => [
                'color' => $request->color,
                'size' => $request->size,
                'material' => $request->material,
                'custom_name' => $request->custom_name,
                'custom_description' => $request->custom_description,
            ],
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
            'orders.*.quantity' => 'required|integer|min:1',
            'orders.*.color' => 'nullable|string|max:100',
            'orders.*.size' => 'nullable|string|max:100',
            'orders.*.material' => 'nullable|string|max:100',
            'orders.*.custom_name' => 'nullable|string|max:100',
            'orders.*.custom_description' => 'nullable|string|max:255',
        ]);

        // Save or update delivery info
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
                'customization_details' => [
                    'color' => $order['color'] ?? null,
                    'size' => $order['size'] ?? null,
                    'material' => $order['material'] ?? null,
                    'custom_name' => $order['custom_name'] ?? null,
                    'custom_description' => $order['custom_description'] ?? null,
                ],
            ]);

            $product->decrement('stock', $order['quantity']);
            $product->increment('total_sold', $order['quantity']);
        }

        return redirect()->route('my-orders')->with('success', 'Bulk order placed successfully!');
    }
}
