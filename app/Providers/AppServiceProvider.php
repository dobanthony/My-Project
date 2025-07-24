<?php

namespace App\Providers;

use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Inertia::share([
            //Authenticated user
            'auth' => function () {
                return [
                    'user' => Auth::user(),
                ];
            },

            // Laravel notifications
            'notifications' => function () {
                if (Auth::check()) {
                    return Auth::user()->notifications->map(function ($n) {
                        return [
                            'id' => $n->id,
                            'message' => $n->data['message'] ?? '',
                            'read_at' => $n->read_at,
                            'created_at' => $n->created_at->diffForHumans(),
                        ];
                    });
                }
                return [];
            },

            // Unread message badge count (custom)
            'unreadMessagesCount' => function () {
                if (Auth::check()) {
                    return Message::where('receiver_id', Auth::id())
                        ->where('is_read', false)
                        ->count();
                }
                return 0;
            },
             // Pending orders count (new)
            'pendingOrdersCount' => function () {
                if (Auth::check()) {
                    return \App\Models\Order::where('status', 'pending')
                        ->whereHas('product.shop', function ($query) {
                            $query->where('user_id', Auth::id());
                        })
                        ->count();
                }
                return 0;
            },
        ]);

        // Optional Vite prefetching
        Vite::prefetch(concurrency: 3);
    }
}
