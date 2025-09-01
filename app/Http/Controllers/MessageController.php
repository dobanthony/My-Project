<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MessageController extends Controller
{
    public function userInbox(Shop $shop, Request $request)
    {
        $userId = auth()->id();

        // ✅ Mark messages as read
        Message::where('shop_id', $shop->id)
            ->where('receiver_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // ✅ Fetch conversation
        $messages = Message::where('shop_id', $shop->id)
            ->where(function ($query) use ($userId) {
                $query->where('sender_id', $userId)
                      ->orWhere('receiver_id', $userId);
            })
            ->with(['sender', 'receiver', 'product'])
            ->orderBy('created_at')
            ->get();

        // ✅ Attach report status to each product inside messages
        $messages->transform(function ($msg) use ($userId) {
            if ($msg->product) {
                $order = Order::where('product_id', $msg->product->id)
                    ->where('user_id', $userId)
                    ->latest()
                    ->first();

                $msg->product->is_reported   = $order?->reported ?? false;
                $msg->product->report_message = $order?->report_message ?? null;
            }
            return $msg;
        });

        // ✅ Determine pinned product
        $pinnedProduct = null;
        if ($request->has('product_id')) {
            $pinnedProduct = Product::find($request->input('product_id'));
        }
        if (!$pinnedProduct) {
            $pinnedProduct = $messages->whereNotNull('product_id')->first()?->product;
        }

        // ✅ Attach report status to pinned product
        if ($pinnedProduct) {
            $order = Order::where('user_id', $userId)
                ->where('product_id', $pinnedProduct->id)
                ->latest()
                ->first();

            $pinnedProduct->is_reported   = $order?->reported ?? false;
            $pinnedProduct->report_message = $order?->report_message ?? null;
        }

        // ✅ Auto-message if "msg" param exists
        if ($request->has('msg') && $pinnedProduct) {
            $existing = Message::where('shop_id', $shop->id)
                ->where('sender_id', $userId)
                ->where('receiver_id', $shop->user_id)
                ->where('message', $request->input('msg'))
                ->where('product_id', $pinnedProduct->id)
                ->first();

            if (!$existing) {
                Message::create([
                    'shop_id'     => $shop->id,
                    'sender_id'   => $userId,
                    'receiver_id' => $shop->user_id,
                    'message'     => $request->input('msg'),
                    'product_id'  => $pinnedProduct->id,
                    'is_read'     => false,
                ]);

                // Refresh messages
                $messages = Message::where('shop_id', $shop->id)
                    ->where(function ($query) use ($userId) {
                        $query->where('sender_id', $userId)
                              ->orWhere('receiver_id', $userId);
                    })
                    ->with(['sender', 'receiver', 'product'])
                    ->orderBy('created_at')
                    ->get();

                // Apply report status again after refresh
                $messages->transform(function ($msg) use ($userId) {
                    if ($msg->product) {
                        $order = Order::where('product_id', $msg->product->id)
                            ->where('user_id', $userId)
                            ->latest()
                            ->first();

                        $msg->product->is_reported   = $order?->reported ?? false;
                        $msg->product->report_message = $order?->report_message ?? null;
                    }
                    return $msg;
                });
            }
        }

        return Inertia::render('User/Inbox', [
            'shop'          => $shop,
            'messages'      => $messages,
            'pinnedProduct' => $pinnedProduct,
        ]);
    }

    // public function sellerInbox(User $user)
    // {
    //     $shop = auth()->user()->shop;

    //     // ✅ Mark messages as read
    //     Message::where('shop_id', $shop->id)
    //         ->where('receiver_id', auth()->id())
    //         ->where('is_read', false)
    //         ->update(['is_read' => true]);

    //     // ✅ Fetch messages
    //     $messages = Message::where('shop_id', $shop->id)
    //         ->where(function ($query) use ($user) {
    //             $query->where('sender_id', $user->id)
    //                   ->orWhere('receiver_id', $user->id);
    //         })
    //         ->with(['sender', 'receiver', 'product'])
    //         ->orderBy('created_at')
    //         ->get();

    //     // ✅ Attach report status for seller
    //     $messages->transform(function ($msg) use ($user) {
    //         if ($msg->product) {
    //             $order = Order::where('product_id', $msg->product->id)
    //                 ->where('user_id', $user->id)
    //                 ->latest()
    //                 ->first();

    //             $msg->product->is_reported   = $order?->reported ?? false;
    //             $msg->product->report_message = $order?->report_message ?? null;
    //         }
    //         return $msg;
    //     });

    //     $pinnedProduct = $messages->whereNotNull('product_id')->first()?->product;

    //     if ($pinnedProduct) {
    //         $order = Order::where('user_id', $user->id)
    //             ->where('product_id', $pinnedProduct->id)
    //             ->latest()
    //             ->first();

    //         $pinnedProduct->is_reported   = $order?->reported ?? false;
    //         $pinnedProduct->report_message = $order?->report_message ?? null;
    //     }

    //     return Inertia::render('Seller/Inbox', [
    //         'shop'          => $shop,
    //         'messages'      => $messages,
    //         'pinnedProduct' => $pinnedProduct,
    //     ]);
    // }
    public function sellerInbox(User $user)
{
    $shop = auth()->user()->shop;

    // ✅ Mark messages as read
    Message::where('shop_id', $shop->id)
        ->where('receiver_id', auth()->id())
        ->where('is_read', false)
        ->update(['is_read' => true]);

    // ✅ Fetch messages
    $messages = Message::where('shop_id', $shop->id)
        ->where(function ($query) use ($user) {
            $query->where('sender_id', $user->id)
                  ->orWhere('receiver_id', $user->id);
        })
        ->with(['sender', 'receiver', 'product'])
        ->orderBy('created_at')
        ->get();

    // ✅ Attach report status for each product
    $messages->transform(function ($msg) use ($user) {
        if ($msg->product) {
            $order = Order::where('product_id', $msg->product->id)
                ->where('user_id', $user->id) // buyer’s order
                ->latest()
                ->first();

            $msg->product->is_reported    = $order?->reported ?? false;
            $msg->product->report_message = $order?->report_message ?? null;
        }
        return $msg;
    });

    // ✅ Ensure pinned product always includes report info
    $pinnedProduct = $messages->whereNotNull('product_id')->first()?->product;

    if ($pinnedProduct) {
        $order = Order::where('user_id', $user->id)
            ->where('product_id', $pinnedProduct->id)
            ->latest()
            ->first();

        $pinnedProduct->is_reported    = $order?->reported ?? false;
        $pinnedProduct->report_message = $order?->report_message ?? null;
    }

    return Inertia::render('Seller/Inbox', [
        'shop'          => $shop,
        'messages'      => $messages,
        'pinnedProduct' => $pinnedProduct,
    ]);
}


    // public function send(Request $request)
    // {
    //     $data = $request->validate([
    //         'shop_id'     => 'required|exists:shops,id',
    //         'message'     => 'required|string',
    //         'receiver_id' => 'required|exists:users,id',
    //         'product_id'  => 'nullable|exists:products,id',
    //     ]);

    //     Message::create([
    //         'shop_id'     => $data['shop_id'],
    //         'sender_id'   => auth()->id(),
    //         'receiver_id' => $data['receiver_id'],
    //         'message'     => $data['message'],
    //         'product_id'  => $data['product_id'] ?? null,
    //         'is_read'     => false,
    //     ]);

    //     return back();
    // }
    public function send(Request $request)
    {
        $data = $request->validate([
            'shop_id'     => 'required|exists:shops,id',
            'message'     => 'required|string',
            'receiver_id' => 'required|exists:users,id',
            'product_id'  => 'nullable|exists:products,id',
        ]);

        $message = Message::create([
            'shop_id'     => $data['shop_id'],
            'sender_id'   => auth()->id(),
            'receiver_id' => $data['receiver_id'],
            'message'     => $data['message'],
            'product_id'  => $data['product_id'] ?? null,
            'is_read'     => false,
        ]);

        // ✅ Attach product report status if applicable
        if ($message->product_id) {
            $order = Order::where('product_id', $message->product_id)
                ->where('user_id', auth()->id())
                ->latest()
                ->first();

            if ($order) {
                $message->product->is_reported    = $order->reported ?? false;
                $message->product->report_message = $order->report_message ?? null;
            }
        }

        return back();
    }

    // Inbox list for user
    public function userInboxList()
    {
        $userId = auth()->id();

        $shopIds = Message::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->pluck('shop_id')
            ->unique();

        $shops = Shop::whereIn('id', $shopIds)
            ->with(['messages' => function ($query) use ($userId) {
                $query->where(function ($q) use ($userId) {
                    $q->where('sender_id', $userId)
                      ->orWhere('receiver_id', $userId);
                })
                ->latest()
                ->limit(1)
                ->with('sender');
            }])
            ->get()
            ->map(function ($shop) {
                return [
                    'id'               => $shop->id,
                    'shop_name'        => $shop->shop_name,
                    'shop_description' => $shop->shop_description,
                    'shop_logo'        => $shop->shop_logo,
                    'latest_message'   => $shop->messages->first(),
                ];
            });

        return Inertia::render('User/InboxList', [
            'shops' => $shops,
        ]);
    }

    // Inbox list for seller
    public function sellerInboxList()
    {
        $shop = auth()->user()->shop;

        $userIds = Message::where('shop_id', $shop->id)
            ->pluck('sender_id')
            ->merge(Message::where('shop_id', $shop->id)->pluck('receiver_id'))
            ->unique()
            ->filter(fn($id) => $id !== auth()->id());

        $users = User::whereIn('id', $userIds)
            ->get()
            ->map(function ($user) use ($shop) {
                $latest = Message::where('shop_id', $shop->id)
                    ->where(function ($q) use ($user) {
                        $q->where('sender_id', $user->id)
                          ->orWhere('receiver_id', $user->id);
                    })
                    ->orderByDesc('created_at')
                    ->first();

                $user->latest_message = $latest;
                return $user;
            });

        return Inertia::render('Seller/InboxList', [
            'users' => $users,
        ]);
    }

    public function unreadCount()
    {
        $count = Message::where('receiver_id', auth()->id())
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }
}
