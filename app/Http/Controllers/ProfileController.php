<?php

namespace App\Http\Controllers;

use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user()->load('branch');

        return view('profile.edit', compact('user'));
    }

    public function edit(Request $request)
    {
        return redirect()->route('profile.show');
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $oldValues = $user->only(['name', 'email']);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
            $user->last_password_changed_at = now();
            $user->must_change_password = false;
        }

        $user->save();

        AuditLogService::logDataChange(
            $request,
            'update',
            'profile',
            $user->id,
            $oldValues,
            $user->only(['name', 'email']),
            'User updated their own profile'
        );

        return redirect()->route('profile.show')->with('success', 'Profile updated successfully.');
    }
}
