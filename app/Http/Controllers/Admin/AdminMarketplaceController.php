<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Notifications\ProductDeletedByAdmin;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class AdminMarketplaceController extends Controller
{
    // Display list of products with sorting
    public function index(Request $request)
    {
        $sort = $request->query('sort', 'all'); // default sort is 'all'
        $cutoff = Carbon::now()->subDays(2); // 2-day threshold

        // Load shop and its user (seller)
        $query = Product::with(['shop.user']);

        if ($sort === 'newest') {
            $query->where('created_at', '>=', $cutoff)->orderBy('created_at', 'desc');
        } elseif ($sort === 'oldest') {
            $query->where('created_at', '<', $cutoff)->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return Inertia::render('Admin/Marketplace/Index', [
            'products' => $query->get(),
            'sort' => $sort,
        ]);
    }

    // Show a single product in the detailed view page
    public function show(Product $product)
    {
        $product->load(['shop.user']); // also load seller through shop

        return Inertia::render('Admin/Marketplace/View', [
            'product' => $product,
        ]);
    }

    // Delete a product
    public function destroy(Request $request, Product $product)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        // Load the related shop and user (seller)
        $product->load('shop.user');
        $seller = optional($product->shop)->user;

        // Fallback if no seller is found
        if (!$seller) {
            return redirect()->route('admin.marketplace.index')
                ->with('error', 'Seller not found for this product.');
        }

        // Send notification to seller before deleting
        $seller->notify(new ProductDeletedByAdmin(
            $product->name,
            $request->reason,
            $product->id
        ));

        // Delete product
        $product->delete();

        // Flash message
        session()->flash('message', 'Product deleted. Seller will be notified.');

        return redirect()->route('admin.marketplace.index');
    }
}
