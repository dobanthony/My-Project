<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\Announcement;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Admin
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        //Seller
        if ($user->role === 'seller') {
            $shop = $user->shop;

            if ($shop) {
                return redirect()->route('seller.analytics');
            }

            return Inertia::render('Seller/CreateShop', [
                'user' => $user,
            ]);
        }
        
        //user
        return Inertia::render('User/Dashboard', [
            'user' => $user,
            'announcements' => Announcement::latest()->take(6)->get(),
        ]);
    }
}
