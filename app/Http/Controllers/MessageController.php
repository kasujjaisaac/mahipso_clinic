<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $messages = Message::with(['sender', 'recipient'])
            ->where(fn ($query) => $query->where('sender_id', auth()->id())->orWhere('recipient_id', auth()->id()))
            ->latest()
            ->paginate(30);

        return view('messages.index', compact('messages'));
    }

    public function create()
    {
        $users = User::when(! auth()->user()->isSuperAdmin(), fn ($query) => $query->where('branch_id', auth()->user()->branch_id))
            ->whereKeyNot(auth()->id())
            ->orderBy('name')
            ->get();

        return view('messages.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        User::when(! $request->user()->isSuperAdmin(), fn ($query) => $query->where('branch_id', $request->user()->branch_id))
            ->whereKey($validated['recipient_id'])
            ->firstOrFail();

        $message = Message::create([
            'sender_id' => auth()->id(),
            'recipient_id' => $validated['recipient_id'],
            'subject' => $validated['subject'],
            'body' => $validated['body'],
        ]);
        return redirect()->route('messages.index')->with('success', 'Message sent successfully.');
    }
}
