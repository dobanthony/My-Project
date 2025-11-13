<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Notifications\ProductDeletedByAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class AdminMarketplaceController extends Controller
{
    public function show($id)
    {
        $product = Product::with('shop')->findOrFail($id);

        return inertia('Admin/Marketplace/View', [
            'product' => $product,
        ]);
    }

    // public function index(Request $request)
    // {
    //     $sort = $request->query('sort', 'all');
    //     $cutoff = Carbon::now()->subDays(2);

    //     $query = Product::with(['shop.user'])->whereNull('deleted_at'); // Only active

    //     if ($sort === 'newest') {
    //         $query->where('created_at', '>=', $cutoff)->orderBy('created_at', 'desc');
    //     } elseif ($sort === 'oldest') {
    //         $query->where('created_at', '<', $cutoff)->orderBy('created_at', 'asc');
    //     } else {
    //         $query->orderBy('created_at', 'desc');
    //     }

    //     return Inertia::render('Admin/Marketplace/Index', [
    //         'products' => $query->get(),
    //         'sort' => $sort,
    //     ]);
    // }
public function index(Request $request)
{
    $sort = $request->query('sort', 'all');
    $search = $request->query('search');
    $cutoff = now()->subDays(2);

    $query = Product::with(['shop.user'])->whereNull('deleted_at'); // Only active

    // 🔍 Search filter
    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhereHas('shop', function ($shopQuery) use ($search) {
                  $shopQuery->where('shop_name', 'like', "%{$search}%");
              });
        });
    }

    // 🕒 Sort filters
    if ($sort === 'newest') {
        $query->where('created_at', '>=', $cutoff)->orderBy('created_at', 'desc');
    } elseif ($sort === 'oldest') {
        $query->where('created_at', '<', $cutoff)->orderBy('created_at', 'asc');
    } else {
        $query->orderBy('created_at', 'desc');
    }

    // 📄 Pagination → Show 10 products per page
    $products = $query->paginate(10)->withQueryString();

    return Inertia::render('Admin/Marketplace/Index', [
        'products' => $products,
        'sort' => $sort,
        'filters' => [
            'search' => $search,
        ],
    ]);
}



    public function archived()
    {
        $products = Product::onlyTrashed()->with(['shop.user'])->orderBy('deleted_at', 'desc')->get();

        return Inertia::render('Admin/Marketplace/Archived', [
            'products' => $products,
        ]);
    }

    public function archive(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.marketplace.index')->with('message', 'Product archived successfully.');
    }

    public function restore($id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        $product->restore();

        return redirect()->route('admin.marketplace.archived')->with('message', 'Product restored successfully.');
    }

    public function forceDelete(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $product = Product::onlyTrashed()->with('shop.user')->findOrFail($id);
        $seller = optional($product->shop)->user;

        if ($seller) {
            $seller->notify(new ProductDeletedByAdmin(
                $product->name,
                $request->reason,
                $product->id
            ));
        }

        $product->forceDelete();

        return redirect()->route('admin.marketplace.archived')->with('message', 'Product permanently deleted.');
    }
}
