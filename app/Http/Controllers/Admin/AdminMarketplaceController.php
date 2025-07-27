<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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

        $query = Product::with(['user', 'shop']);

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
        $product->load(['user', 'shop']);

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

        $seller = $product->user;

        $product->delete();

        session()->flash('message', 'Product deleted. Seller will be notified.');

        session()->flash('seller_notification', [
            'title' => 'Product Deleted',
            'message' => 'Your product "' . $product->name . '" was deleted by an admin. Reason: ' . $request->reason,
            'product_id' => $product->id,
        ]);

        return redirect()->route('admin.marketplace.index');
    }
}
