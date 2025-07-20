<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class AdminMarketplaceController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->query('sort', 'all'); // default sort is 'all'
        $cutoff = Carbon::now()->subDays(2); // 2-day threshold

        $query = Product::with(['user', 'shop']);

        if ($sort === 'newest') {
            // Show only products created in the last 2 days
            $query->where('created_at', '>=', $cutoff)->orderBy('created_at', 'desc');
        } elseif ($sort === 'oldest') {
            // Show only products older than 2 days
            $query->where('created_at', '<', $cutoff)->orderBy('created_at', 'asc');
        } else {
            // Show all products, latest first
            $query->orderBy('created_at', 'desc');
        }

        return Inertia::render('Admin/Marketplace/Index', [
            'products' => $query->get(),
            'sort' => $sort,
        ]);
    }


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
