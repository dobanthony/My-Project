<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\CustomizableProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'allow_color' => 'nullable|boolean',
            'allow_size' => 'nullable|boolean',
            'allow_material' => 'nullable|boolean',
            'allow_pattern' => 'nullable|boolean',
            'materials' => 'nullable|array', // Nested customization
        ]);

        // Check if user has a shop
        $shop = Auth::user()->shop;
        if (!$shop) {
            return back()->withErrors([
                'shop' => 'Please create a shop before adding a custom product.'
            ]);
        }

        // Handle product main image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        // Create main product
        $product = Product::create([
            'shop_id' => $shop->id,
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'description' => $validated['description'] ?? null,
            'image' => $validated['image'] ?? null,
        ]);

        // Prepare nested customization options JSON
        $customOptions = [];

        if ($validated['allow_material'] && !empty($validated['materials'])) {
            foreach ($validated['materials'] as $material) {
                $materialData = [
                    'material' => $material['name'],
                    'image' => isset($material['image']) ? $material['image']->store('custom_products', 'public') : null,
                    'colors' => [],
                    'patterns' => [],
                    'sizes' => [],
                ];

                // Colors
                if ($validated['allow_color'] && isset($material['colors'])) {
                    foreach ($material['colors'] as $color) {
                        $materialData['colors'][] = [
                            'name' => $color['name'],
                            'image' => isset($color['image']) ? $color['image']->store('custom_products', 'public') : null,
                        ];
                    }
                }

                // Patterns
                if ($validated['allow_pattern'] && isset($material['patterns'])) {
                    foreach ($material['patterns'] as $pattern) {
                        $materialData['patterns'][] = [
                            'name' => $pattern['name'],
                            'image' => isset($pattern['image']) ? $pattern['image']->store('custom_products', 'public') : null,
                        ];
                    }
                }

                // Sizes
                if ($validated['allow_size'] && isset($material['sizes'])) {
                    foreach ($material['sizes'] as $size) {
                        $materialData['sizes'][] = [
                            'name' => $size['name'],
                            'image' => isset($size['image']) ? $size['image']->store('custom_products', 'public') : null,
                        ];
                    }
                }

                $customOptions[] = $materialData;
            }
        }

        // Create customizable product in single table
        CustomizableProduct::create([
            'product_id' => $product->id,
            'allow_color' => $validated['allow_color'] ?? false,
            'allow_size' => $validated['allow_size'] ?? false,
            'allow_material' => $validated['allow_material'] ?? false,
            'allow_pattern' => $validated['allow_pattern'] ?? false,
            'custom_options' => $customOptions,
        ]);

        return redirect()
            ->route('seller.products.index')
            ->with('success', 'Custom product created.');
    }
}
