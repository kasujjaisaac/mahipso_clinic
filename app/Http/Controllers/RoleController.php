<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            abort_unless($request->user()->isSuperAdmin(), 403);

            return $next($request);
        });
    }

    public function index()
    {
        $roles = Role::with('users')->paginate(20);
        $modules = Role::moduleOptions();
        return view('roles.index', compact('roles', 'modules'));
    }

    public function create()
    {
        $modules = Role::moduleOptions();
        $selectedModules = [];
        return view('roles.create', compact('modules', 'selectedModules'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'nullable|string',
            'module_access' => 'nullable|array',
            'module_access.*' => 'string|in:' . implode(',', array_keys(Role::moduleOptions())),
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
            'description' => $validated['description'] ?? null,
            'module_access' => array_values($validated['module_access'] ?? []),
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('roles.show', $role)->with('success', 'Role created successfully.');
    }

    public function show($id)
    {
        $role = Role::with('users')->findOrFail($id);
        $modules = Role::moduleOptions();
        $selectedModules = $role->allowedModules();
        return view('roles.show', compact('role', 'modules', 'selectedModules'));
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $modules = Role::moduleOptions();
        $selectedModules = $role->allowedModules();
        return view('roles.edit', compact('role', 'modules', 'selectedModules'));
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'description' => 'nullable|string',
            'module_access' => 'nullable|array',
            'module_access.*' => 'string|in:' . implode(',', array_keys(Role::moduleOptions())),
        ]);

        $role->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'module_access' => array_values($validated['module_access'] ?? []),
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('roles.show', $role)->with('success', 'Role updated successfully.');
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        return redirect()->route('roles.index')->with('success', 'Role deleted.');
    }
}
