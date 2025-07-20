<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\ShopRating;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
                'limit' => (int) $request->input('limit', 30),
                'shop' => null,
            ]);
        }

        $shopId = $user->shop->id;
        $search = $request->input('search');
        $limit = $request->input('limit', 30);

        $query = Product::where('shop_id', $shopId);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                ->orWhere('description', 'like', "%$search%");
            });
        }

        $products = $query
            ->orderBy('id', 'desc')
            ->paginate($limit)
            ->withQueryString(); // ensures search/limit stays on page change

        return Inertia::render('Seller/Products', [
            'products' => $products,
            'search' => $search,
            'limit' => $limit,
            'shop' => $user->shop,
        ]);
    }


    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user->shop) {
            return back()->with('error', 'Please create a shop first.');
        }

        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'description' => 'nullable',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['name', 'price', 'stock', 'description']);
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

        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return back()->with('success', 'Product deleted successfully!');
    }

    public function showPublic(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('perPage', 12); // default items per page

        $query = Product::with('shop.user');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                ->orWhere('description', 'like', "%$search%");
            });
        }

        $products = $query->orderBy('id', 'desc')->paginate($perPage)->withQueryString();

        return Inertia::render('User/View', [
            'products' => $products,
            'search' => $search,
        ]);
    }


    public function show($id)
    {
        //Eager load shop and its ratings for shop_rating accessor to work
        $product = Product::with(['shop.ratings', 'customization'])->findOrFail($id);


        $user = auth()->user();
        $shop = $product->shop;

        //Get all product-specific ratings with user data
        $ratings = ShopRating::with('user')
            ->where('product_id', $product->id)
            ->latest()
            ->get();

        //Compute product average rating
        $averageRating = round($ratings->avg('product_rating'), 1);
        $ratingsCount = $ratings->count();

        //Count of approved orders (total sold)
        $totalSold = Order::where('product_id', $product->id)
            ->where('status', 'approved')
            ->count();

        return Inertia::render('User/ProductDetails', [
            'product' => $product,
            'ratings' => $ratings,
            'averageRating' => $averageRating,
            'ratingsCount' => $ratingsCount,
            'totalSold' => $totalSold,
            'isFollowing' => $user
                ? $user->followedShops()->where('shop_id', $shop->id)->exists()
                : false,
            'followerCount' => $shop->followers()->count(),
        ]);
    }

}
