<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class SellerApprovalController extends Controller
{
    public function index()
    {
        $applications = User::where('seller_status', 'pending')->get();

        return Inertia::render('Admin/SellerApplications', [
            'applications' => $applications,
        ]);
    }

    public function show(User $user)
    {
        // ✅ Decode JSON so Vue gets an array
        $user->valid_id_photos = $user->valid_id_photos
            ? json_decode($user->valid_id_photos, true)
            : [];

        return Inertia::render('Admin/ViewSellerApplication', [
            'user' => $user,
        ]);
    }

    public function approve(User $user)
    {
        $user->update([
            'seller_status' => 'approved',
            'role' => 'seller',
        ]);

        return back()->with('message', '✅ Seller approved successfully!');
    }

    public function decline(User $user)
    {
        // ✅ Delete uploaded ID photos if they exist
        if (!empty($user->valid_id_photos)) {
            $photos = json_decode($user->valid_id_photos, true);
            foreach ($photos as $photo) {
                if (Storage::disk('public')->exists($photo)) {
                    Storage::disk('public')->delete($photo);
                }
            }
        }

        // ✅ Update user status
        $user->update([
            'seller_status' => 'declined',
            'valid_id_photos' => null, // remove reference from DB
        ]);

        return back()->with('message', '❌ Application declined and ID photos deleted.');
    }
}
