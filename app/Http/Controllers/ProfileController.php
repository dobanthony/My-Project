<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User; 
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name'    => ['required', 'string', 'max:255'],
            'middle_name'   => ['nullable', 'string', 'max:255'],
            'last_name'     => ['required', 'string', 'max:255'],
            'email'         => ['required', 'email', 'max:255'],
            'phone'         => ['nullable', 'string', 'max:20'],
            'address'       => ['nullable', 'string', 'max:255'],
            'dob'           => ['nullable', 'date'],
            'social_links'  => ['nullable', 'array'],
            'social_links.facebook' => ['nullable', 'string', 'url'],
            'social_links.twitter'  => ['nullable', 'string', 'url'],
        ]);

        $user = $request->user();

        if ($user->email !== $validated['email']) {
            $user->email_verified_at = null;
        }

        $user->fill($validated);
        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }


    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    // public function updateAvatar(Request $request)
    // {
    //     $request->validate([
    //         'avatar' => 'required|image|max:2048',
    //     ]);

    //     /** @var \App\Models\User $user */
    //     $user = Auth::user();

    //      // Delete old avatar if it exists
    //     if ($user->avatar && Storage::exists('public/' . $user->avatar)) {
    //         Storage::delete('public/' . $user->avatar);
    //     }

    //     // store avatar in /storage/app/public/avatars
    //     $path = $request->file('avatar')->store('avatars', 'public');

    //     // save to user
    //     $user->avatar = $path;
    //     $user->save();

    //     return back()->with('success', 'Profile photo updated.');
    // }
    public function updateAvatar(Request $request)
{
    $request->validate([
        'avatar' => 'required|image|max:2048',
    ]);

    $user = Auth::user();

    // Delete old avatar if exists
    if ($user->avatar && Storage::exists('public/' . $user->avatar)) {
        Storage::delete('public/' . $user->avatar);
    }

    // Store new avatar
    $path = $request->file('avatar')->store('avatars', 'public');

    $user->avatar = $path;
    $user->save();

    return response()->json([
        'message' => 'Profile photo updated.',
        'avatar' => $path,
    ]);
}


}
