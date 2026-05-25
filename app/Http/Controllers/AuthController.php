<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Services\AuditLogService;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $candidate = User::where('email', $credentials['email'])->first();
        if ($candidate?->locked_until && $candidate->locked_until->isFuture()) {
            AuditLogService::logFailedLoginAttempt($request, $credentials['email']);
            return back()->withErrors([
                'email' => 'This account is temporarily locked. Try again later or contact an administrator.',
            ])->onlyInput('email');
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = $request->user();
            $user->forceFill([
                'failed_login_count' => 0,
                'locked_until' => null,
            ])->save();

            // Log successful login with comprehensive details
            AuditLogService::logLogin($request, true);

            return redirect()->intended(
                $user->isSuperAdmin()
                    ? route('admin.dashboard')
                    : route('dashboard.role')
            );
        }

        // Log failed login attempt
        AuditLogService::logFailedLoginAttempt($request, $credentials['email']);

        if ($candidate) {
            $failedCount = $candidate->failed_login_count + 1;
            $candidate->forceFill([
                'failed_login_count' => $failedCount,
                'locked_until' => $failedCount >= 5 ? now()->addMinutes(15) : null,
            ])->save();
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        // Log logout with session duration
        AuditLogService::logLogout($request);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
