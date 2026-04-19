<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;

class MessageController extends Controller
{
    public function index()
    {
        $messages = Message::with(['sender', 'recipient'])->latest()->paginate(30);
        return view('messages.index', compact('messages'));
    }

    public function create()
    {
        $users = User::all();
        return view('messages.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
        ]);
        $message = Message::create([
            'sender_id' => auth()->id(),
            'recipient_id' => $validated['recipient_id'],
            'subject' => $validated['subject'],
            'body' => $validated['body'],
        ]);
        return redirect()->route('messages.index')->with('success', 'Message sent successfully.');
    }
}
