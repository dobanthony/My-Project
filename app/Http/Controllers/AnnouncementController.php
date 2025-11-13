<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AnnouncementController extends Controller
{
    // Display user-facing announcements
    public function index()
    {
        return Inertia::render('User/Dashboard', [
            'user' => Auth::user(),
            'announcements' => Announcement::latest()->take(100)->get(),
        ]);
    }

    // Display admin announcement management page
    public function adminIndex()
    {
        return Inertia::render('Admin/AdminAnnouncement', [ // ✅ renamed back to your file name
            'announcements' => Announcement::latest()->get(['id', 'title', 'content', 'created_at']),
        ]);
    }

    // Display archived announcements (soft deleted)
    public function archived()
    {
        return Inertia::render('Admin/AnnouncementArchive', [
            'announcements' => Announcement::onlyTrashed()
                ->latest('deleted_at')
                ->get(['id', 'title', 'content', 'deleted_at']),
        ]);
    }

    // Store new announcement
    public function store(Request $request)
    {
        $request->validate([
            'title'   => 'required|max:255',
            'content' => 'required',
        ]);

        Announcement::create($request->only('title', 'content'));

        return back()->with('success', 'Announcement created.');
    }

    // Update announcement
    public function update(Request $request, $id)
    {
        $announcement = Announcement::findOrFail($id);

        $announcement->update($request->only('title', 'content'));

        return back()->with('success', 'Announcement updated.');
    }

    // Archive (soft delete)
    public function destroy($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->delete(); // Soft delete

        return back()->with('success', 'Announcement archived.');
    }

    // Restore archived
    public function restore($id)
    {
        $announcement = Announcement::onlyTrashed()->findOrFail($id);
        $announcement->restore();

        return back()->with('success', 'Announcement restored.');
    }

    // Permanently delete
    public function forceDelete($id)
    {
        $announcement = Announcement::onlyTrashed()->findOrFail($id);
        $announcement->forceDelete();

        return back()->with('success', 'Announcement permanently deleted.');
    }
}
