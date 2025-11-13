<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\ShopRating;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Category;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index(Request $request)
{
    $user = auth()->user();

    if (!$user->shop) {
        return Inertia::render('Seller/Products', [
            'products' => [
                'data' => [],
                'links' => [],
            ],
            'search' => $request->input('search', ''),
            'limit' => (int) $request->input('limit', 10),
            'category' => $request->input('category', ''), // ✅ ensure this is always returned
            'shop' => null,
            'categories' => Category::all(),
        ]);
    }

    $shopId = $user->shop->id;
    $search = $request->input('search');
    $limit = $request->input('limit', 10);
    $category = $request->input('category');

    $query = Product::with('category')->where('shop_id', $shopId);

    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%$search%")
              ->orWhere('description', 'like', "%$search%");
        });
    }

    if ($category) {
        $query->where('category_id', $category);
    }

    $products = $query
        ->orderBy('id', 'desc')
        ->paginate($limit)
        ->withQueryString(); // ✅ keeps query params (search, category, limit) during pagination

    return Inertia::render('Seller/Products', [
        'products' => $products,
        'search' => $search,
        'limit' => $limit,
        'category' => $category, // ✅ added
        'shop' => $user->shop,
        'categories' => Category::all(),
    ]);
}

    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user->shop) {
            return back()->with('error', 'Please create a shop first.');
        }

        $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric',
            'stock'       => 'required|integer',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|max:2048',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $data = $request->only(['name', 'price', 'stock', 'description', 'category_id']);
        $data['shop_id'] = $user->shop->id;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($data);

        return back()->with('success', 'Product added!');
    }


    public function update(Request $request, Product $product)
    {
        if ($product->shop_id !== auth()->user()->shop->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric',
            'stock'       => 'required|integer',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|max:2048',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        return back()->with('success', 'Product updated successfully!');
    }

    public function destroy(Product $product)
    {
        if ($product->shop_id !== auth()->user()->shop->id) {
            abort(403);
        }

        // if ($product->image && Storage::disk('public')->exists($product->image)) {
        //     Storage::disk('public')->delete($product->image);
        // }

        $product->delete();

        return back()->with('success', 'Product deleted successfully!');
    }

public function showPublic(Request $request)
{
    $search = $request->input('search');
    $perPage = $request->input('perPage', 10);
    $category = $request->input('category');

    $query = Product::with(['shop.user', 'category', 'customizableProduct'])
        ->withAvg('productRatings', 'product_rating')
        ->withCount(['productRatings as ratings_count'])
        ->withSum(['orders as total_sold' => function ($q) {
            $q->where('status', 'approved');
        }], 'quantity');

    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    if ($category) {
        $query->where('category_id', $category);
    }

    $products = $query
        ->orderBy('id', 'desc')
        ->paginate($perPage)
        ->withQueryString()
        ->through(fn ($p) => [
            'id'            => $p->id,
            'name'          => $p->name,
            'description'   => $p->description,
            'price'         => $p->price,
            'stock'         => $p->stock, 
            'image'         => $p->image,
            'average_rating'=> $p->product_ratings_avg_product_rating
                                ? round($p->product_ratings_avg_product_rating, 1)
                                : 0.0,
            'ratings_count' => $p->ratings_count ?? 0,
            'total_sold'    => $p->total_sold ?? 0,
            'category'      => [
                'id'   => $p->category?->id,
                'name' => $p->category?->name ?? '—',
            ],
            'shop' => [
                'user' => [
                    'first_name' => $p->shop?->user?->first_name,
                    'last_name'  => $p->shop?->user?->last_name,
                ],
                'address' => $p->shop?->address,
            ],
            'customizable_product' => $p->customizableProduct ? [
                'allow_color'    => $p->customizableProduct->allow_color,
                'allow_size'     => $p->customizableProduct->allow_size,
                'allow_material' => $p->customizableProduct->allow_material,
                'allow_pattern'  => $p->customizableProduct->allow_pattern,
            ] : null,
        ]);

    return Inertia::render('User/View', [
        'products'    => $products,
        'search'      => $search,
        'category'    => $category,
        'categories'  => \App\Models\Category::select('id', 'name')->get(),
    ]);
}

    public function show($id)
    {
        $product = Product::with(['shop.ratings', 'customization', 'category'])->findOrFail($id);

        $user = auth()->user();
        $shop = $product->shop;

        $ratings = ShopRating::with('user')
            ->where('product_id', $product->id)
            ->latest()
            ->get();

        $averageRating = round($ratings->avg('product_rating'), 1);
        $ratingsCount  = $ratings->count();

        // Sum the total quantity sold, not just count orders
        $totalSold = Order::where('product_id', $product->id)
            ->where('status', 'approved')
            ->sum('quantity');


        return Inertia::render('User/ProductDetails', [
            'product' => [
                'id'            => $product->id,
                'name'          => $product->name,
                'description'   => $product->description,
                'price'         => $product->price,
                'stock'         => $product->stock,
                'image'         => $product->image,
                'customizable_product'   => $product->customizableProduct,
                'customization' => $product->customization,
                'shop'          => $product->shop,
                'category'      => $product->category ? $product->category->name : null,
            ],
            'ratings'       => $ratings,
            'averageRating' => $averageRating,
            'ratingsCount'  => $ratingsCount,
            'totalSold'     => $totalSold, // fixed value
            'isFollowing'   => $user ? $user->followedShops()->where('shop_id', $shop->id)->exists() : false,
            'followerCount' => $shop->followers()->count(),
        ]);
    }


        public function showWelcomeProducts(Request $request)
    {
        $search = $request->input('search');

        $products = Product::with(['shop.user'])
            ->withAvg('productRatings', 'product_rating')
            ->withCount(['productRatings as ratings_count'])
            ->withSum(['orders as total_sold' => function ($q) {
                $q->where('status', 'approved');
            }], 'quantity')
            ->when($search, function($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->orderBy('id', 'desc')
            ->take(8)
            ->get()
            ->map(fn($p) => [
                'id'            => $p->id,
                'name'          => $p->name,
                'description'   => $p->description,
                'price'         => $p->price,
                'image'         => $p->image,
                'eco_friendly'  => $p->eco_friendly,
                'average_rating'=> $p->product_ratings_avg_product_rating
                                    ? round($p->product_ratings_avg_product_rating, 1)
                                    : 0.0,
                'ratings_count' => $p->ratings_count ?? 0,
                'total_sold'    => $p->total_sold ?? 0,
                'shop' => [
                    'user' => [
                        'first_name' => $p->shop?->user?->first_name,
                        'last_name'  => $p->shop?->user?->last_name,
                    ],
                    'address' => $p->shop?->address,
                ],
            ]);

        return Inertia::render('Welcome', [
            'products' => $products,
        ]);
    }

    public function archived(Request $request)
    {
        $user = auth()->user();

        if (!$user->shop) {
            return Inertia::render('Seller/ArchivedProducts', [
                'products' => ['data' => [], 'links' => []],
                'shop' => null,
                'categories' => Category::all(['id', 'name']),
                'search' => $request->input('search', ''),
                'category_id' => $request->input('category_id', ''),
                'limit' => (int) $request->input('limit', 10),
            ]);
        }

        $shopId = $user->shop->id;
        $search = $request->input('search');
        $limit = $request->input('limit', 10);
        $categoryId = $request->input('category_id');

        $query = Product::onlyTrashed()
            ->where('shop_id', $shopId)
            ->with('category'); // ✅ Load category relationship

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $products = $query
            ->orderBy('id', 'desc')
            ->paginate($limit)
            ->withQueryString()
            ->through(fn ($p) => [
                'id'           => $p->id,
                'name'         => $p->name,
                'description'  => $p->description,
                'price'        => $p->price,
                'stock'        => $p->stock,
                'eco_friendly' => $p->eco_friendly,
                'category'     => $p->category ? $p->category->name : null,
                'image'        => $p->image ? asset('storage/' . $p->image) : null,
            ]);

        return Inertia::render('Seller/ArchivedProducts', [
            'products' => $products,
            'categories' => Category::all(['id', 'name']),
            'search'   => $search,
            'category_id' => $categoryId,
            'limit'    => $limit,
            'shop'     => $user->shop,
        ]);
    }

    public function restore($id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);

        if ($product->shop_id !== auth()->user()->shop->id) {
            abort(403);
        }

        $product->restore();

        return back()->with('success', 'Product restored successfully!');
    }

    public function forceDelete($id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);

        if ($product->shop_id !== auth()->user()->shop->id) {
            abort(403);
        }

        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->forceDelete();

        return back()->with('success', 'Product permanently deleted!');
    }


    public function showGuest($id)
    {
        $product = Product::with([
            'shop:id,shop_name,shop_logo,shop_description,phone_number,email_address,created_at',
            'customization.customOptions.colors',
            'customization.customOptions.sizes',
            'customization.customOptions.patterns'
        ])->findOrFail($id);

        $ratings = ShopRating::where('product_id', $id)
            ->with('user:id,first_name,last_name,avatar')
            ->latest()
            ->get();

        $averageRating = number_format($ratings->avg('product_rating') ?? 0, 1);
        $ratingsCount = $ratings->count();

        $totalSold = $product->orders()
            ->where('status', 'approved')
            ->sum('quantity');

        $followerCount = $product->shop->followers()->count();

        return Inertia::render('ProductGuest', [
            'product'        => $product,
            'ratings'        => $ratings,
            'averageRating'  => $averageRating,
            'ratingsCount'   => $ratingsCount,
            'totalSold'      => $totalSold,
            'isFollowing'    => false,
            'followerCount'  => $followerCount,
        ]);
    }
}