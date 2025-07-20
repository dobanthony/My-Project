<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class SellerApprovalController extends Controller
{
    public function index()
    {
        $applications = User::where('seller_status', 'pending')->get();

        return \Inertia\Inertia::render('Admin/SellerApplications', [
            'applications' => $applications,
        ]);
    }

    public function approve(User $user)
    {
        $user->update([
            'seller_status' => 'approved',
            'role' => 'seller',
        ]);

        return back()->with('message', 'Seller approved!');
    }

    public function decline(User $user)
    {
        $user->update([
            'seller_status' => 'declined',
        ]);

        return back()->with('message', 'Application declined.');
    }
}
