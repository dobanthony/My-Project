<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\DeliveryInfo;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    /**
     * Show the single checkout form (Buy Now / Customized Buy Now)
     */
    public function show(Request $request, Product $product)
    {
        $quantity = (int) $request->input('quantity', 1);

        // Fetch user's latest delivery info for autofill
        $lastDeliveryInfo = auth()->user()->deliveryInfos()->latest()->first();

        // Extract customization details from query string
        $customizations = [
            'color' => $request->input('color'),
            'size' => $request->input('size'),
            'material' => $request->input('material'),
            'pattern' => $request->input('pattern'),
            'custom_name' => $request->input('custom_name'),
            'custom_description' => $request->input('custom_description'),
            'selected_image' => $request->input('selected_image'), // ✅ image passed from ProductDetails
        ];

        return Inertia::render('Checkout/Form', [
            'product' => $product->load('customization'),
            'quantity' => $quantity,
            'lastDeliveryInfo' => $lastDeliveryInfo,
            'customizations' => $customizations,
        ]);
    }

    /**
     * Default create form if accessed directly (fallback)
     */
    public function create(Product $product)
    {
        $lastDeliveryInfo = auth()->user()->deliveryInfos()->latest()->first();

        return Inertia::render('Checkout/Form', [
            'product' => $product->load('customization'),
            'quantity' => request('quantity', 1),
            'lastDeliveryInfo' => $lastDeliveryInfo,
            'customizations' => [
                'color' => null,
                'size' => null,
                'material' => null,
                'pattern' => null,
                'custom_name' => null,
                'custom_description' => null,
                'selected_image' => null,
            ],
        ]);
    }

    /**
     * Bulk checkout form (for cart checkout)
     */
    public function bulkForm(Request $request)
    {
        $cartItems = collect($request->input('items', []));
        $lastDeliveryInfo = auth()->user()->deliveryInfos()->latest()->first();

        return Inertia::render('Checkout/BulkForm', [
            'cartItems' => $cartItems,
            'lastDeliveryInfo' => $lastDeliveryInfo,
        ]);
    }

    /**
     * Store single order
     */
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

            // Customization
            'color' => 'nullable|string|max:100',
            'size' => 'nullable|string|max:100',
            'material' => 'nullable|string|max:100',
            'pattern' => 'nullable|string|max:100',
            'custom_name' => 'nullable|string|max:255',
            'custom_description' => 'nullable|string',
            'selected_image' => 'nullable|string|max:255',
        ]);

        $product = Product::findOrFail($request->product_id);

        // ✅ Stock check
        if ($product->stock < $request->quantity) {
            return back()->withErrors(['quantity' => 'Not enough stock available.']);
        }

        // ✅ Save delivery info
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

        // ✅ Create order with customizations
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
                'pattern' => $request->pattern,
                'custom_name' => $request->custom_name,
                'custom_description' => $request->custom_description,
                'selected_image' => $request->selected_image, // ✅ stores image
            ],
        ]);

        // ✅ Update product stock
        $product->decrement('stock', $request->quantity);
        $product->increment('total_sold', $request->quantity);

        return redirect()->route('my-orders')->with('success', '✅ Order placed! Waiting for approval.');
    }

    /**
     * Store bulk orders (cart checkout)
     */
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
            'orders.*.pattern' => 'nullable|string|max:100',
            'orders.*.selected_image' => 'nullable|string|max:255',
        ]);

        // Save delivery info
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

        foreach ($request->orders as $orderData) {
            $product = Product::findOrFail($orderData['product_id']);

            if ($product->stock < $orderData['quantity']) {
                return back()->withErrors([
                    'orders' => "Not enough stock for product: {$product->name}"
                ]);
            }

            Order::create([
                'user_id' => auth()->id(),
                'product_id' => $product->id,
                'quantity' => $orderData['quantity'],
                'status' => 'pending',
                'full_name' => $request->full_name,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'delivery_address' => $request->delivery_address,
                'notes' => $request->notes,
                'customization_details' => [
                    'color' => $orderData['color'] ?? null,
                    'size' => $orderData['size'] ?? null,
                    'material' => $orderData['material'] ?? null,
                    'pattern' => $orderData['pattern'] ?? null,
                    'selected_image' => $orderData['selected_image'] ?? null,
                ],
            ]);

            $product->decrement('stock', $orderData['quantity']);
            $product->increment('total_sold', $orderData['quantity']);
        }

        return redirect()->route('my-orders')->with('success', '🛒 Bulk order placed successfully!');
    }
}
