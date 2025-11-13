<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\Category;
use App\Models\ReceivedOrder;
use App\Models\Review;
use App\Notifications\OrderIssueReported;
use App\Notifications\OrderReceivedNotification;
use App\Notifications\OrderStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Notifications\OrderCanceledNotification;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function store(Request $request)
{
    $validated = $request->validate([
        'product_id' => 'required|exists:products,id',
        'quantity' => 'required|integer|min:1',
        'customization_details' => 'nullable|array',
        'customization_details.color' => 'nullable|string|max:100',
        'customization_details.size' => 'nullable|string|max:100',
        'customization_details.material' => 'nullable|string|max:100',
        'customization_details.custom_name' => 'nullable|string|max:255',
        'customization_details.custom_description' => 'nullable|string|max:500',
        'customization_details.custom_image' => 'nullable|image|max:5120', // 5MB
    ]);

    $product = Product::findOrFail($validated['product_id']);

    // ✅ Stock check — just check, don’t deduct yet
    if ($product->stock < $validated['quantity']) {
        return back()->withErrors(['quantity' => 'Not enough stock available.']);
    }

    // ✅ Handle custom uploaded image
    $customImagePath = null;
    if ($request->hasFile('customization_details.custom_image')) {
        $customImagePath = $request->file('customization_details.custom_image')->store('customizations', 'public');
    }

    // ✅ Merge image into customization details
    $customization = $validated['customization_details'] ?? [];
    if ($customImagePath) {
        $customization['custom_image'] = $customImagePath;
    }

    // ✅ Create order without affecting stock
    $order = Order::create([
        'user_id' => auth()->id(),
        'product_id' => $product->id,
        'quantity' => $validated['quantity'],
        'status' => 'pending',
        'customization_details' => $customization,
    ]);

    return back()->with('success', 'Order placed successfully. Waiting for seller approval.');
}


    public function sellerOrders(Request $request)
{
    $search = $request->input('search');
    $status = $request->input('status');
    $categoryId = $request->input('category_id'); // 🆕 add category filter

    $query = Order::with(['product.category', 'user'])
        ->whereHas('product.shop', fn($q) =>
            $q->where('user_id', auth()->id())
        );

    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->whereHas('product', fn($sub) =>
                $sub->where('name', 'like', "%{$search}%")
            )->orWhereHas('user', fn($sub) =>
                $sub->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
            )->orWhere('status', 'like', "%{$search}%");
        });
    }

    if ($status) {
        $query->where('status', $status);
    }

    // 🆕 Filter by category
    if ($categoryId) {
        $query->whereHas('product', fn($p) =>
            $p->where('category_id', $categoryId)
        );
    }

    $orders = $query->latest()->paginate(25)->withQueryString();

    // 🆕 Get all categories for dropdown
    $categories = \App\Models\Category::orderBy('name')->get(['id', 'name']);

    return Inertia::render('Seller/Orders', [
        'orders' => $orders,
        'categories' => $categories, // 🆕 send to Vue
        'filters' => $request->only('search', 'status', 'category_id'),
    ]);
}


public function approve(Request $request, Order $order)
{
    DB::transaction(function () use ($order, $request) {
        $product = $order->product;

        if ($product->stock < $order->quantity) {
            throw new \Exception('Not enough stock to approve this order.');
        }

        // ✅ Deduct stock only upon approval
        $product->decrement('stock', $order->quantity);
        $product->increment('total_sold', $order->quantity);

        $order->update([
            'status' => 'approved',
            'delivery_date' => $request->delivery_date,
        ]);
    });

    return redirect()->back(303)->with('success', 'Order approved and stock updated.');
}


    public function decline(Order $order)
    {
        $order->update(['status' => 'declined']);

        $order->user->notify(new OrderStatusNotification($order, 'Your order has been declined.'));

        return back()->with('error', 'Order declined.');
    }

    public function show(Order $order)
    {
        return Inertia::render('Receipt', [
            'order' => $order->load('product.category', 'product.shop.user', 'user', 'deliveryInfo.province',
            'deliveryInfo.municipality','deliveryInfo.barangay'),
        ]);
    }

public function myOrders(Request $request)
{
    $search = $request->input('search');
    $limit = (int) $request->input('limit', 10);

    $query = Order::with(['product.shop.user', 'receivedOrder'])
        ->where('user_id', auth()->id());

    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->whereHas('product', fn($sub) =>
                $sub->where('name', 'like', "%{$search}%")
            )->orWhere('status', 'like', "%{$search}%")
            ->orWhereHas('product.shop.user', fn($sub) =>
                $sub->where('name', 'like', "%{$search}%")
            );
        });
    }

    // 🧠 Transform orders to include full image path for customization
    $orders = $query->latest()->paginate($limit)->through(function ($order) {
        $custom = $order->customization_details ?? [];

        // ✅ Add full URL for custom image if exists
        if (isset($custom['custom_image'])) {
            $custom['custom_image_url'] = asset('storage/' . $custom['custom_image']);
        }

        // ✅ Add fallback product image
        $order->customization_details = $custom;
        $order->display_image = $custom['custom_image_url'] ?? 
                                ($order->product && $order->product->image 
                                    ? asset('storage/' . $order->product->image) 
                                    : 'https://via.placeholder.com/150');

        return $order;
    });

    return Inertia::render('Orders/MyOrders', [
        'orders' => $orders,
        'search' => $search,
    ]);
}

    public function sellerReceipt(Order $order)
    {
        try {
            return Inertia::render('Seller/Orders/Receipt', [
                'order' => $order->load('user', 'product.shop.user'),
                'userId' => auth()->id(),
                'isSeller' => true,
            ]);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function storeBulk(Request $request)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*.product_id' => 'required|exists:products,id',
            'orders.*.quantity' => 'required|integer|min:1',
        ]);

        foreach ($request->orders as $data) {
            $product = Product::findOrFail($data['product_id']);

            if ($product->stock < $data['quantity']) {
                return back()->withErrors(['message' => "Not enough stock for {$product->name}"]);
            }

            Order::create([
                'user_id' => auth()->id(),
                'product_id' => $product->id,
                'quantity' => $data['quantity'],
                'status' => 'pending'
            ]);

            $product->decrement('stock', $data['quantity']);
            $product->increment('total_sold', $data['quantity']);
        }

        return back()->with('success', 'Bulk orders placed successfully.');
    }

    public function destroy(Order $order)
    {
        if ($order->product->shop->user_id !== auth()->id()) {
            abort(403);
        }

        $order->delete();

        return back()->with('success', 'Order deleted.');
    }

    public function markAsReceived(Order $order)
    {
        if ($order->receivedOrder) {
            return back()->with('message', 'You already marked this as received.');
        }

        ReceivedOrder::create([
            'order_id' => $order->id,
            'user_id' => auth()->id(),
            'received_at' => now(),
        ]);

        $seller = $order->product->shop->user;
        $seller->notify(new OrderReceivedNotification($order));

        return back()->with('success', 'Marked as received. Seller notified.');
    }

    public function updateDeliveryStatus(Request $request, Order $order)
    {
        if ($order->product->shop->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'delivery_status' => 'required|in:pending,delivered,failed'
        ]);

        $order->update([
            'delivery_status' => $validated['delivery_status']
        ]);

        return back()->with('success', 'Delivery status updated.');
    }

    public function submitReview(Request $request, Order $order)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
            'product_id' => 'required|exists:products,id',
            'photo' => 'nullable|image|max:15360', // 15 MB
        ]);

        if (Review::where('user_id', auth()->id())->where('order_id', $order->id)->exists()) {
            return back()->with('message', 'You already reviewed this order.');
        }

        $photoPath = $request->hasFile('photo')
            ? $request->file('photo')->store('review_photos', 'public')
            : null;

        Review::create([
            'user_id' => auth()->id(),
            'product_id' => $request->product_id,
            'order_id' => $order->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'photo' => $photoPath,
        ]);

        return back()->with('message', 'Review submitted successfully.');
    }

    public function cancel($id)
    {
        $order = Order::with('product.shop.user')->findOrFail($id);

        // Update order status
        $order->status = 'canceled';
        $order->save();

        // Notify seller
        $seller = $order->product->shop->user;
        if ($seller) {
            $seller->notify(new OrderCanceledNotification($order));
        }

        return back()->with('success', 'Order has been canceled.');
    }

    public function sellerView(Order $order)
    {
        if ($order->product->shop->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load([
            'user',
            'product.category', // ✅ include category
            'product.shop.user',
            'deliveryInfo.province',
            'deliveryInfo.municipality',
            'deliveryInfo.barangay'
        ]);

        return Inertia::render('Seller/View', [
            'order' => $order->toArray(),
            'isSeller' => true,
        ]);
    }
    
    public function receipt(Order $order)
    {
        $user = auth()->user();

        $order->load('user', 'product.category', 'product.shop.user', 'receivedOrder', 'deliveryInfo.province',
        'deliveryInfo.municipality','deliveryInfo.barangay');

        $isSeller = $order->product->shop->user_id === $user->id;

        return Inertia::render('Receipt', [
            'order' => $order,
            'userId' => $user->id,
            'isSeller' => $isSeller,
            'hasReported' => (bool) $order->reported,
        ]);
    }

    public function reportIssue(Request $request, Order $order)
    {
        $request->validate([
            'message' => 'required|min:5',
        ]);

        if ($order->reported) {
            return back()->with('error', 'You have already reported this order.');
        }

        $order->update([
            'reported' => true,
            'report_message' => $request->message,
        ]);

        $seller = $order->product->shop->user;
        $seller->notify(new OrderIssueReported($order, $request->message));

        return back()->with('success', 'Issue reported.');
    }

}
