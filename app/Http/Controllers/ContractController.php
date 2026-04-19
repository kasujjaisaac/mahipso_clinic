<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function index() { return view('contracts.index'); }
    public function create() { return view('contracts.create'); }
    public function store(Request $request) { /* ... */ }
    public function show($id) { return view('contracts.show'); }
    public function edit($id) { return view('contracts.edit'); }
    public function update(Request $request, $id) { /* ... */ }
    public function destroy($id) { /* ... */ }
}
