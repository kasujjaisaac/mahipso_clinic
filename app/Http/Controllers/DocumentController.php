<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            abort_unless($request->user()->canAccessModule('programs'), 403);

            return $next($request);
        });
    }

    public function index() {
        $documents = \App\Models\Document::with('user')->latest()->paginate(20);
        return view('documents.index', compact('documents'));
    }

    public function create() {
        return view('documents.create');
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,doc,docx,png,jpg,jpeg',
            'description' => 'nullable|string',
        ]);
        $path = $request->file('file')->store('documents', 'public');
        $document = \App\Models\Document::create([
            'title' => $validated['title'],
            'file_path' => $path,
            'description' => $validated['description'] ?? null,
            'user_id' => auth()->id(),
        ]);
        return redirect()->route('documents.show', $document)->with('success', 'Document uploaded successfully.');
    }

    public function show($id) {
        $document = \App\Models\Document::with('user')->findOrFail($id);
        return view('documents.show', compact('document'));
    }

    public function destroy($id) {
        $document = \App\Models\Document::findOrFail($id);
        $document->delete();
        return redirect()->route('documents.index')->with('success', 'Document deleted.');
    }
}
