<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\CustomizableProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CustomProductController extends Controller
{
    /**
     * Show the form to create a new customizable product.
     */
    public function create()
    {
        return Inertia::render('Seller/CreateCustomProduct');
    }

    /**
     * Store a newly created customizable product in storage.
     */
    public function store(Request $request)
    {
        // ✅ Validate input including allow_description
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'allow_color' => 'nullable|boolean',
            'allow_size' => 'nullable|boolean',
            'allow_material' => 'nullable|boolean',
            'allow_name' => 'nullable|boolean',
            'allow_description' => 'nullable|boolean', // ✅ NEW
        ]);

        // ✅ Check if user has a shop
        $shop = Auth::user()->shop;
        if (!$shop) {
            return back()->withErrors([
                'shop' => 'Please create a shop before adding a custom product.'
            ]);
        }

        // ✅ Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        // ✅ Create product
        $product = Product::create([
            'shop_id' => $shop->id,
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'description' => $validated['description'] ?? null,
            'image' => $validated['image'] ?? null,
        ]);

        // ✅ Create customization options (with allow_description)
        CustomizableProduct::create([
            'product_id' => $product->id,
            'allow_color' => $validated['allow_color'] ?? false,
            'allow_size' => $validated['allow_size'] ?? false,
            'allow_material' => $validated['allow_material'] ?? false,
            'allow_name' => $validated['allow_name'] ?? false,
            'allow_description' => $validated['allow_description'] ?? false, // ✅ NEW
        ]);

        return redirect()
            ->route('seller.products.index')
            ->with('success', 'Custom product created.');
    }
}
