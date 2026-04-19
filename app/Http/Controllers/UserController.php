<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    protected function authorizeSuperAdmin(): void
    {
        abort_unless(Auth::user()->isSuperAdmin(), 403);
    }

    public function index(Request $request)
    {
        $this->authorizeSuperAdmin();

        $query = User::with('branch');

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('name')->paginate(15);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $this->authorizeSuperAdmin();

        $branches = Branch::active()->orderBy('name')->get();
        $roles = Role::whereIn('name', ['super_admin', 'branch_admin', 'doctor', 'nurse', 'receptionist', 'counselor', 'pharmacist', 'labtech'])->get();

        return view('users.create', compact('branches', 'roles'));
    }

    public function store(Request $request)
    {
        $this->authorizeSuperAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        if ($validated['role'] !== 'super_admin' && empty($validated['branch_id'])) {
            return back()->withInput()->withErrors(['branch_id' => 'Branch is required for this role.']);
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'branch_id' => $validated['branch_id'] ?? null,
        ]);

        $user->syncRoles([$validated['role']]);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $this->authorizeSuperAdmin();

        $branches = Branch::active()->orderBy('name')->get();
        $roles = Role::whereIn('name', ['super_admin', 'branch_admin', 'doctor', 'nurse', 'receptionist', 'counselor', 'pharmacist', 'labtech'])->get();

        $userRole = $user->getRoleNames()->first();

        return view('users.edit', compact('user', 'branches', 'roles', 'userRole'));
    }

    public function show(User $user)
    {
        $this->authorizeSuperAdmin();

        return view('users.show', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeSuperAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        if ($validated['role'] !== 'super_admin' && empty($validated['branch_id'])) {
            return back()->withInput()->withErrors(['branch_id' => 'Branch is required for this role.']);
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->branch_id = $validated['branch_id'] ?? null;

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();
        $user->syncRoles([$validated['role']]);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $this->authorizeSuperAdmin();

        if (Auth::id() === $user->id) {
            return redirect()->route('users.index')->with('danger', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
