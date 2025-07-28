<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Notifications\OrderCanceledNotification;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderCancelNotificationController extends Controller
{
    // 📨 List only cancel-related notifications
    public function index()
    {
        $notifications = auth()->user()->notifications()
            ->where('type', OrderCanceledNotification::class)
            ->latest()
            ->get();

        return Inertia::render('Seller/Notifications/CancelNotifications', [
            'notifications' => $notifications
        ]);
    }

    // ✅ Mark a specific notification as read
    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return redirect()->to('/receipt/' . $notification->data['order_id']);
    }

    // ✅ Mark all cancel-related notifications as read
    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications()
            ->where('type', OrderCanceledNotification::class)
            ->update(['read_at' => now()]);

        return back()->with('success', 'All cancel notifications marked as read.');
    }
}
