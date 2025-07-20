<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Notifications\OrderStatusNotification;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserNotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()->notifications()
            ->where('type', OrderStatusNotification::class)
            ->latest()
            ->get();

        return Inertia::render('Notifications/Notifications', [
            'notifications' => $notifications,
        ]);
    }

    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return redirect()->to('/receipt/' . $notification->data['order_id']);
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return back()->with('success', 'All notifications marked as read.');
    }
}
