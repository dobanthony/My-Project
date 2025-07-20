<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class NotificationController extends Controller
{
    //Show all seller notifications
    public function index()
    {
        $notifications = Auth::user()->notifications()
            ->latest()
            ->get()
            ->map(function ($n) {
                return [
                    'id' => $n->id,
                    'message' => $n->data['message'],
                    'order_id' => $n->data['order_id'] ?? null,
                    'read_at' => $n->read_at,
                    'created_at' => $n->created_at->diffForHumans()
                ];
            });

        return Inertia::render('Seller/Notifications/Index', [
            'notifications' => $notifications
        ]);
    }

    //Mark one notification as read
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead(); // sets read_at timestamp
        return back(); // Inertia will reload the component state
    }

    //Mark all notifications as read
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead(); // works on unread only
        return back()->with('success', 'All notifications marked as read.');
    }
}
