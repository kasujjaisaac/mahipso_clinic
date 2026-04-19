<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index() { return view('departments.index'); }
    public function create() { return view('departments.create'); }
    public function store(Request $request) { /* ... */ }
    public function show($id) { return view('departments.show'); }
    public function edit($id) { return view('departments.edit'); }
    public function update(Request $request, $id) { /* ... */ }
    public function destroy($id) { /* ... */ }
}
