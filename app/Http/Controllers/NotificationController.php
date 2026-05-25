<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\User;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            abort_unless($request->user()->hasRole(['super_admin', 'branch_admin']), 403);

            return $next($request);
        })->only(['create', 'store', 'destroy']);
    }

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

        if (! empty($validated['user_id'])) {
            User::when(! $request->user()->isSuperAdmin(), fn ($query) => $query->where('branch_id', $request->user()->branch_id))
                ->whereKey($validated['user_id'])
                ->firstOrFail();
        }

        $notification = \App\Models\Notification::create($validated);
        return redirect()->route('notifications.show', $notification)->with('success', 'Notification created successfully.');
    }

    public function index()
    {
        $notifications = Notification::where(function ($query) {
                $query->whereNull('user_id')->orWhere('user_id', auth()->id());
            })
            ->latest()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function show($id)
    {
        $notification = Notification::where(function ($query) {
                $query->whereNull('user_id')->orWhere('user_id', auth()->id());
            })
            ->findOrFail($id);

        return view('notifications.show', compact('notification'));
    }

    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->delete();
        return redirect()->route('notifications.index')->with('success', 'Notification deleted.');
    }
}
