<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\ReceivedOrder;
use App\Models\Review;
use App\Notifications\OrderIssueReported;
use App\Notifications\OrderReceivedNotification;
use App\Notifications\OrderStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Notifications\OrderCanceledNotification;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $product = Product::findOrFail($validated['product_id']);

        // Stock check
        if ($product->stock < $validated['quantity']) {
            return back()->withErrors(['quantity' => 'Not enough stock available.']);
        }

        // Place order
        Order::create([
            'user_id' => auth()->id(),
            'product_id' => $product->id,
            'quantity' => $validated['quantity'],
            'status' => 'pending',
        ]);

        // Update stock and sold
        $product->decrement('stock', $validated['quantity']);
        $product->increment('total_sold', $validated['quantity']);

        return back()->with('success', 'Order placed. Waiting for seller approval.');
    }

    public function sellerOrders(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $query = Order::with(['product', 'user'])
            ->whereHas('product.shop', fn($q) =>
                $q->where('user_id', auth()->id())
            );

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('product', fn($sub) =>
                    $sub->where('name', 'like', "%{$search}%")
                )->orWhereHas('user', fn($sub) =>
                    $sub->where('name', 'like', "%{$search}%")
                )->orWhere('status', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        return Inertia::render('Seller/Orders', [
            'orders' => $query->latest()->paginate(10)->withQueryString(),
            'filters' => $request->only('search', 'status'),
        ]);
    }

    public function approve(Request $request, Order $order)
    {
        if ($order->status !== 'pending') {
            return back()->with('error', 'This order has already been processed.');
        }

        $request->validate([
            'delivery_date' => 'required|date|after_or_equal:today'
        ]);

        $product = $order->product;

        // Re-check stock (for safety)
        if ($product->stock < $order->quantity) {
            return back()->with('error', 'Not enough stock to approve the order.');
        }

        // Deduct only if not already deducted on creation
        // Optional safety: you may track a 'deducted' field or skip this if already handled

        $order->update([
            'status' => 'approved',
            'delivery_date' => $request->delivery_date
        ]);

        // Notify buyer
        $order->user->notify(new OrderStatusNotification($order, '✅ Your order has been approved!'));

        return back()->with('success', 'Order approved with delivery date.');
    }

    public function decline(Order $order)
    {
        $order->update(['status' => 'declined']);

        $order->user->notify(new OrderStatusNotification($order, '❌ Your order has been declined.'));

        return back()->with('error', 'Order declined.');
    }

    public function show(Order $order)
    {
        return Inertia::render('Receipt', [
            'order' => $order->load('product', 'user', 'product.shop.user')
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

        return Inertia::render('Orders/MyOrders', [
            'orders' => $query->latest()->paginate($limit)->withQueryString(),
            'search' => $search,
        ]);
    }

    // public function receipt(Order $order)
    // {
    //     $user = auth()->user();

    //     $order->load('user', 'product.shop.user', 'receivedOrder');
    //     $isSeller = $order->product->shop->user_id === $user->id;

    //     return Inertia::render('Receipt', [
    //         'order' => $order,
    //         'userId' => $user->id,
    //         'isSeller' => $isSeller
    //     ]);
    // }

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

    // public function reportIssue(Request $request, Order $order)
    // {
    //     $request->validate([
    //         'message' => 'required|min:5',
    //     ]);

    //     $seller = $order->product->shop->user;
    //     $seller->notify(new OrderIssueReported($order, $request->message));

    //     return back()->with('success', 'Issue reported.');
    // }

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
            'product.shop.user',
        ]);

        return Inertia::render('Seller/View', [
            'order' => $order->toArray(), // ✅ ensure customization_details gets included
            'isSeller' => true,
        ]);
    }


    public function receipt(Order $order)
    {
        $user = auth()->user();

        $order->load('user', 'product.shop.user', 'receivedOrder');
        $isSeller = $order->product->shop->user_id === $user->id;

        return Inertia::render('Receipt', [
            'order' => $order,
            'userId' => $user->id,
            'isSeller' => $isSeller,
            'hasReported' => (bool) $order->reported, // ✅ ensure boolean
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
