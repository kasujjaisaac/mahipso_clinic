<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            abort_unless($request->user()->canAccessModule('human_resources'), 403);

            return $next($request);
        });
    }

    public function index() { return view('leaves.index'); }
    public function create() { return view('leaves.create'); }
    public function store(Request $request) { /* ... */ }
    public function show($id) { return view('leaves.show'); }
    public function edit($id) { return view('leaves.edit'); }
    public function update(Request $request, $id) { /* ... */ }
    public function destroy($id) { /* ... */ }
}
