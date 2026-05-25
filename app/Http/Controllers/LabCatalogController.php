<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\LabCatalog;
use App\Models\ServiceItem;
use Illuminate\Http\Request;

class LabCatalogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            abort_unless($request->user()->canAccessModule('laboratory'), 403);

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $tests = LabCatalog::visibleTo($request->user())->with('branch')->orderBy('test_name')->paginate(30);
        return view('lab_catalog.index', compact('tests'));
    }

    public function create()
    {
        $branches = auth()->user()->isSuperAdmin() ? Branch::active()->orderBy('name')->get() : Branch::whereKey(auth()->user()->branch_id)->get();
        return view('lab_catalog.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'branch_id' => 'nullable|exists:branches,id',
            'test_name' => 'required|string|max:255',
            'sample_type' => 'nullable|string|max:255',
            'unit' => 'nullable|string|max:100',
            'reference_range' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'is_active' => 'nullable|boolean',
        ]);
        $data['branch_id'] = $request->user()->isSuperAdmin() ? ($data['branch_id'] ?? null) : $request->user()->branch_id;
        $data['is_active'] = $request->boolean('is_active', true);
        $test = LabCatalog::create($data);
        ServiceItem::firstOrCreate(
            ['branch_id' => $test->branch_id, 'name' => $test->test_name, 'category' => 'laboratory'],
            ['price' => $test->price, 'is_active' => true]
        );
        return redirect()->route('lab-catalog.index')->with('success', 'Lab test saved.');
    }
}
