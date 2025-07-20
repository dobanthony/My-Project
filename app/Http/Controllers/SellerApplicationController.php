<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class SellerApplicationController extends Controller
{
    public function showForm()
    {
        // return Inertia::render('User/ApplySeller');
        return Inertia::render('User/ApplySeller', [
            'user' => auth()->user(),
        ]);
    }

    public function apply(Request $request)
    {
        $request->validate([
            'application_reason' => 'required|string|max:1000',
        ]);

        $user = Auth::user();

        $user->update([
            'application_reason' => $request->application_reason,
            'seller_status' => 'pending',
        ]);

        return redirect()->back()->with('message', 'Your application has been submitted!');
    }
}
