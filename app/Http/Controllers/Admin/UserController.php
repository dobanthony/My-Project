<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Users/Index', [
            'users' => User::latest()->get(['id', 'first_name', 'last_name', 'email', 'role', 'created_at']),
        ]);
    }

    public function show(User $user)
    {
        return Inertia::render('Admin/Users/View', ['user' => $user]);
    }

    public function edit(User $user)
    {
        return Inertia::render('Admin/Users/Edit', ['user' => $user]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'dob' => 'nullable|date',
            'social_links.facebook' => 'nullable|url',
            'social_links.twitter' => 'nullable|url',
            'role' => 'required|in:user,seller,admin',
        ]);

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'User updated Successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted.');
    }
}
