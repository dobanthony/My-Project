<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SellerApplicationController extends Controller
{
    /**
     * Show seller application form.
     */
    public function showForm()
    {
        return Inertia::render('User/ApplySeller', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Handle the seller application submission.
     */
    public function apply(Request $request)
    {
        $request->validate([
            'application_reason' => 'required|string|max:1000',
            'valid_id_type' => 'required|string|max:255',
            'valid_id_photos.*' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // 2MB max each
        ]);

        $user = Auth::user();

        // Handle multiple ID photo uploads
        $photoPaths = [];
        if ($request->hasFile('valid_id_photos')) {
            foreach ($request->file('valid_id_photos') as $photo) {
                $photoPaths[] = $photo->store('id_photos', 'public');
            }
        }

        // Update user record
        $user->update([
            'application_reason' => $request->application_reason,
            'valid_id_type' => $request->valid_id_type,
            'valid_id_photos' => $photoPaths ? json_encode($photoPaths) : $user->valid_id_photos,
            'seller_status' => 'pending',
        ]);

        return redirect()->back()->with('message', 'Your application has been submitted!');
    }
}
