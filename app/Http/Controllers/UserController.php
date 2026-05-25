<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\Role;

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
        $roles = Role::orderBy('name')->get();
        $supervisors = User::role(['line_supervisor', 'branch_admin', 'super_admin'])->orderBy('name')->get();

        return view('users.create', compact('branches', 'roles', 'supervisors'));
    }

    public function store(Request $request)
    {
        $this->authorizeSuperAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'employee_number' => 'nullable|string|max:100',
            'job_title' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'line_supervisor_id' => 'nullable|exists:users,id',
            'branch_id' => [
                Rule::requiredIf(fn () => $request->input('role') !== 'super_admin'),
                'nullable',
                Rule::exists('branches', 'id')->where('status', 'active'),
            ],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'branch_id' => $validated['branch_id'] ?? null,
            'employee_number' => $validated['employee_number'] ?? null,
            'job_title' => $validated['job_title'] ?? null,
            'department' => $validated['department'] ?? null,
            'line_supervisor_id' => $validated['line_supervisor_id'] ?? null,
            'last_password_changed_at' => now(),
        ]);

        $user->syncRoles([$validated['role']]);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $this->authorizeSuperAdmin();

        $branches = Branch::active()->orderBy('name')->get();
        $roles = Role::orderBy('name')->get();
        $supervisors = User::role(['line_supervisor', 'branch_admin', 'super_admin'])->whereKeyNot($user->id)->orderBy('name')->get();

        $userRole = $user->getRoleNames()->first();

        return view('users.edit', compact('user', 'branches', 'roles', 'userRole', 'supervisors'));
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
            'employee_number' => 'nullable|string|max:100',
            'job_title' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'line_supervisor_id' => ['nullable', 'exists:users,id', Rule::notIn([$user->id])],
            'branch_id' => [
                Rule::requiredIf(fn () => $request->input('role') !== 'super_admin'),
                'nullable',
                Rule::exists('branches', 'id')->where('status', 'active'),
            ],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->branch_id = $validated['branch_id'] ?? null;
        $user->employee_number = $validated['employee_number'] ?? null;
        $user->job_title = $validated['job_title'] ?? null;
        $user->department = $validated['department'] ?? null;
        $user->line_supervisor_id = $validated['line_supervisor_id'] ?? null;

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
            $user->last_password_changed_at = now();
            $user->must_change_password = false;
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
