<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function create()
    {
        return view('notifications.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'type' => 'required|string',
            'scheduled_for' => 'nullable|date',
        ]);
        $notification = \App\Models\Notification::create($validated);
        return redirect()->route('notifications.show', $notification)->with('success', 'Notification created successfully.');
    }

    public function index()
    {
        $notifications = Notification::latest()->paginate(20);
        return view('notifications.index', compact('notifications'));
    }

    public function show($id)
    {
        $notification = Notification::findOrFail($id);
        return view('notifications.show', compact('notification'));
    }

    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->delete();
        return redirect()->route('notifications.index')->with('success', 'Notification deleted.');
    }
}
