<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = $request->user();

            // Log successful login with comprehensive details
            AuditLogService::logLogin($request, true);

            return redirect()->intended(
                $user->isSuperAdmin()
                    ? route('admin.dashboard')
                    : route('branches.cards')
            );
        }

        // Log failed login attempt
        AuditLogService::logFailedLoginAttempt($request, $credentials['email']);

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
