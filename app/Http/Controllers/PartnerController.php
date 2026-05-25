<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Partner;

class PartnerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            abort_unless($request->user()->canAccessModule('programs'), 403);

            return $next($request);
        });
    }

    public function index()
    {
        $partners = Partner::latest()->paginate(20);
        return view('partners.index', compact('partners'));
    }

    public function create()
    {
        return view('partners.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        $partner = Partner::create($validated);
        return redirect()->route('partners.show', $partner)->with('success', 'Partner added successfully.');
    }

    public function show($id)
    {
        $partner = Partner::findOrFail($id);
        return view('partners.show', compact('partner'));
    }

    public function edit($id)
    {
        $partner = Partner::findOrFail($id);
        return view('partners.edit', compact('partner'));
    }

    public function update(Request $request, $id)
    {
        $partner = Partner::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        $partner->update($validated);
        return redirect()->route('partners.show', $partner)->with('success', 'Partner updated successfully.');
    }

    public function destroy($id)
    {
        $partner = Partner::findOrFail($id);
        $partner->delete();
        return redirect()->route('partners.index')->with('success', 'Partner deleted.');
    }
}
