<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\DeliveryInfo;
use App\Models\Province;
use App\Models\Municipality;
use App\Models\Barangay;
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

    $lastDeliveryInfo = auth()->user()->deliveryInfos()->latest()->first();

    $customizations = [
        'color' => $request->input('color'),
        'size' => $request->input('size'),
        'material' => $request->input('material'),
        'pattern' => $request->input('pattern'),
        'custom_name' => $request->input('custom_name'),
        'custom_description' => $request->input('custom_description'),
        'selected_image' => $request->input('selected_image'),
    ];

    $provinces = Province::orderBy('name')->get();
    $municipalities = Municipality::orderBy('name')->get();
    $barangays = Barangay::orderBy('name')->get();

    return Inertia::render('Checkout/Form', [
        'product' => $product->load('customization'),
        'quantity' => $quantity,
        'customizations' => $customizations,
        'provinces' => $provinces,
        'municipalities' => $municipalities,
        'barangays' => $barangays,
        'lastDeliveryInfo' => $lastDeliveryInfo ? [
            'id' => $lastDeliveryInfo->id,
            'full_name' => $lastDeliveryInfo->full_name,
            'phone_number' => $lastDeliveryInfo->phone_number,
            'email' => $lastDeliveryInfo->email,
            'province_id' => $lastDeliveryInfo->province_id,
            'municipality_id' => $lastDeliveryInfo->municipality_id,
            'barangay_id' => $lastDeliveryInfo->barangay_id,
            'street_address' => $lastDeliveryInfo->street_address,
            'notes' => $lastDeliveryInfo->notes,
        ] : null,
    ]);
}

    /**
     * Default create form if accessed directly (fallback)
     */
public function create(Product $product)
{
    $lastDeliveryInfo = auth()->user()->deliveryInfos()->latest()->first();

    $provinces = Province::orderBy('name')->get();
    $municipalities = Municipality::orderBy('name')->get();
    $barangays = Barangay::orderBy('name')->get();

    return Inertia::render('Checkout/Form', [
        'product' => $product->load('customization'),
        'quantity' => request('quantity', 1),
        'customizations' => [
            'color' => null,
            'size' => null,
            'material' => null,
            'pattern' => null,
            'custom_name' => null,
            'custom_description' => null,
            'selected_image' => null,
        ],
        'provinces' => $provinces,
        'municipalities' => $municipalities,
        'barangays' => $barangays,
        'lastDeliveryInfo' => $lastDeliveryInfo ? [
            'id' => $lastDeliveryInfo->id,
            'full_name' => $lastDeliveryInfo->full_name,
            'phone_number' => $lastDeliveryInfo->phone_number,
            'email' => $lastDeliveryInfo->email,
            'province_id' => $lastDeliveryInfo->province_id,
            'municipality_id' => $lastDeliveryInfo->municipality_id,
            'barangay_id' => $lastDeliveryInfo->barangay_id,
            'street_address' => $lastDeliveryInfo->street_address,
            'notes' => $lastDeliveryInfo->notes,
        ] : null,
    ]);
}

    /**
     * Bulk checkout form (for cart checkout)
     */
    public function bulkForm(Request $request)
    {
        $cartItems = collect($request->input('items', []));
        $lastDeliveryInfo = auth()->user()->deliveryInfos()->latest()->first();

        $provinces = Province::orderBy('name')->get();
        $municipalities = Municipality::orderBy('name')->get();
        $barangays = Barangay::orderBy('name')->get();

        return Inertia::render('Checkout/BulkForm', [
            'cartItems' => $cartItems,
            'lastDeliveryInfo' => $lastDeliveryInfo,
            'provinces' => $provinces,
            'municipalities' => $municipalities,
            'barangays' => $barangays,
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
            'province_id' => 'required|exists:provinces,id',
            'municipality_id' => 'required|exists:municipalities,id',
            'barangay_id' => 'required|exists:barangays,id',
            'street_address' => 'nullable|string',
            'notes' => 'nullable|string',
            'quantity' => 'required|integer|min:1',
            'color' => 'nullable|string|max:100',
            'size' => 'nullable|string|max:100',
            'material' => 'nullable|string|max:100',
            'pattern' => 'nullable|string|max:100',
            'custom_name' => 'nullable|string|max:255',
            'custom_description' => 'nullable|string',
            'selected_image' => 'nullable|string|max:255',
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
                'province_id' => $request->province_id,
                'municipality_id' => $request->municipality_id,
                'barangay_id' => $request->barangay_id,
                'street_address' => $request->street_address,
                'notes' => $request->notes,
            ]
        );

        Order::create([
            'user_id' => auth()->id(),
            'product_id' => $product->id,
            'full_name' => $request->full_name,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'province_id' => $request->province_id,
            'municipality_id' => $request->municipality_id,
            'barangay_id' => $request->barangay_id,
            'street_address' => $request->street_address,
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
                'selected_image' => $request->selected_image,
            ],
        ]);

        return redirect()->route('my-orders')
            ->with('success', 'Order placed! Waiting for seller approval.');
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
            'province_id' => 'required|exists:provinces,id',
            'municipality_id' => 'required|exists:municipalities,id',
            'barangay_id' => 'required|exists:barangays,id',
            'street_address' => 'nullable|string',
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

        DeliveryInfo::updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'full_name' => $request->full_name,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'province_id' => $request->province_id,
                'municipality_id' => $request->municipality_id,
                'barangay_id' => $request->barangay_id,
                'street_address' => $request->street_address,
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
                'province_id' => $request->province_id,
                'municipality_id' => $request->municipality_id,
                'barangay_id' => $request->barangay_id,
                'street_address' => $request->street_address,
                'notes' => $request->notes,
                'customization_details' => [
                    'color' => $orderData['color'] ?? null,
                    'size' => $orderData['size'] ?? null,
                    'material' => $orderData['material'] ?? null,
                    'pattern' => $orderData['pattern'] ?? null,
                    'selected_image' => $orderData['selected_image'] ?? null,
                ],
            ]);
        }

        return redirect()->route('my-orders')
            ->with('success', 'Bulk order placed! Waiting for seller approval.');
    }
}
