<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\ServiceItem;
use Illuminate\Http\Request;

class ServiceItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            abort_unless($request->user()->canAccessModule('administration'), 403);

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $services = ServiceItem::visibleTo($request->user())->with('branch')->orderBy('category')->orderBy('name')->paginate(30);
        return view('service_items.index', compact('services'));
    }

    public function create()
    {
        $branches = auth()->user()->isSuperAdmin() ? Branch::active()->orderBy('name')->get() : Branch::whereKey(auth()->user()->branch_id)->get();
        return view('service_items.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'branch_id' => 'nullable|exists:branches,id',
            'name' => 'required|string|max:255',
            'category' => 'required|in:consultation,laboratory,procedure,other',
            'price' => 'required|numeric|min:0',
            'is_active' => 'nullable|boolean',
        ]);
        $data['branch_id'] = $request->user()->isSuperAdmin() ? ($data['branch_id'] ?? null) : $request->user()->branch_id;
        $data['is_active'] = $request->boolean('is_active', true);
        ServiceItem::create($data);
        return redirect()->route('service-items.index')->with('success', 'Service saved.');
    }
}
