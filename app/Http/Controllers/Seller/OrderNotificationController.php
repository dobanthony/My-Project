<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Notifications\OrderStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class OrderNotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()->notifications()
            ->where('type', OrderStatusNotification::class)
            ->latest()
            ->get();

        return Inertia::render('Seller/Notifications/Notifications', [
            'notifications' => $notifications
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