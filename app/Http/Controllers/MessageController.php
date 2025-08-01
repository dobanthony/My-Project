<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
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

        // ✅ Fetch all conversation messages
        $messages = Message::where('shop_id', $shop->id)
            ->where(function ($query) use ($userId) {
                $query->where('sender_id', $userId)
                      ->orWhere('receiver_id', $userId);
            })
            ->with(['sender', 'receiver', 'product'])
            ->orderBy('created_at')
            ->get();

        // ✅ FIXED: Prioritize query param over message history
        $pinnedProduct = null;

        if ($request->has('product_id')) {
            $pinnedProduct = Product::find($request->input('product_id'));
        }

        if (!$pinnedProduct) {
            $pinnedProduct = $messages->whereNotNull('product_id')->first()?->product;
        }

        // ✅ Optional: Send auto-message if `msg` param is present
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

                // Re-fetch updated message list
                $messages = Message::where('shop_id', $shop->id)
                    ->where(function ($query) use ($userId) {
                        $query->where('sender_id', $userId)
                              ->orWhere('receiver_id', $userId);
                    })
                    ->with(['sender', 'receiver', 'product'])
                    ->orderBy('created_at')
                    ->get();
            }
        }

        return Inertia::render('User/Inbox', [
            'shop'          => $shop,
            'messages'      => $messages,
            'pinnedProduct' => $pinnedProduct,
        ]);
    }

    public function sellerInbox(User $user)
    {
        $shop = auth()->user()->shop;

        Message::where('shop_id', $shop->id)
            ->where('receiver_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = Message::where('shop_id', $shop->id)
            ->where(function ($query) use ($user) {
                $query->where('sender_id', $user->id)
                      ->orWhere('receiver_id', $user->id);
            })
            ->with(['sender', 'receiver', 'product'])
            ->orderBy('created_at')
            ->get();

        $pinnedProduct = $messages->whereNotNull('product_id')->first()?->product;

        return Inertia::render('Seller/Inbox', [
            'shop' => $shop,
            'messages' => $messages,
            'pinnedProduct' => $pinnedProduct,
        ]);
    }

    public function send(Request $request)
    {
        $data = $request->validate([
            'shop_id'     => 'required|exists:shops,id',
            'message'     => 'required|string',
            'receiver_id' => 'required|exists:users,id',
            'product_id'  => 'nullable|exists:products,id',
        ]);

        Message::create([
            'shop_id'     => $data['shop_id'],
            'sender_id'   => auth()->id(),
            'receiver_id' => $data['receiver_id'],
            'message'     => $data['message'],
            'product_id'  => $data['product_id'] ?? null,
            'is_read'     => false,
        ]);

        return back();
    }

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
