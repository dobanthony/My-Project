<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AnnouncementController extends Controller
{
    // Display user-facing announcements.

    public function index()
    {
        return Inertia::render('User/Dashboard', [ // ✅ Located at: resources/js/Pages/User/Dashboard.vue
            // 'dashboard' => Announcement::latest()->get(),
            'announcements' => Announcement::latest()->take(6)->get(),
        ]);
    }

    //Display admin announcement management page.
    public function adminIndex()
    {
        return Inertia::render('Admin/AdminAnnouncement', [ // ✅ Located at: resources/js/Pages/Admin/AdminAnnouncement.vue
            // 'announcements' => Announcement::latest()->get(),
            'announcements' => Announcement::latest()->get(['id', 'title', 'content', 'created_at']),
        ]);
    }

    //Store a newly created announcement.
    public function store(Request $request)
    {
        $request->validate([
            'title'   => 'required|max:255',
            'content' => 'required',
        ]);

        Announcement::create($request->only('title', 'content'));

        return back()->with('success', 'Announcement created.');
    }

    //Update an existing announcement.
    public function update(Request $request, $id)
    {
        $announcement = Announcement::findOrFail($id);

        $announcement->update($request->only('title', 'content'));

        return back()->with('success', 'Announcement updated.');
    }

    public function destroy($id)
    {
        Announcement::destroy($id);

        return back()->with('success', 'Announcement deleted.');
    }
}
