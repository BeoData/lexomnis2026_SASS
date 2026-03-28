<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LoginController extends Controller
{
    /**
     * Show the login form
     */
    public function show()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        return Inertia::render('Auth/Login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // Regenerate session to prevent session fixation attacks
            $request->session()->regenerate();
            
            // Ensure we have the correct user after login
            $user = Auth::user();
            
            // Clear any cached user data
            Auth::setUser($user);

            // Redirect to the intended URL (if present), otherwise fall back to role-based dashboard.
            $default = $user->isSuperAdmin() ? route('dashboard') : route('client.dashboard');

            // If the request is an Inertia visit, prefer a full-location redirect so the browser performs
            // a normal navigation and any session cookies set by this response are applied before the
            // next page load. This avoids a common SPA timing issue where the XHR follow-up request
            // doesn't include the newly-set session cookie.
            if ($request->header('X-Inertia')) {
                return Inertia::location($default);
            }

            return redirect()->intended($default);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Redirect user based on their role
     */
    protected function redirectByRole($user)
    {
        if ($user->isSuperAdmin()) {
            return redirect()->route('dashboard');
        }

        return redirect()->route('client.dashboard');
    }
}
