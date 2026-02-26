<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Str;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Passwordless login by email or mobile identifier.
     * Logs user in if a matching record exists (no password check).
     */
    public function storeEmailOnly(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'string'],
        ]);

        $identifier = trim($data['email']);

        // detect email vs mobile
        $isEmail = filter_var($identifier, FILTER_VALIDATE_EMAIL) !== false;

        if (! $isEmail) {
            // normalize mobile: keep digits only
            $identifier = preg_replace('/[^0-9]/', '', $identifier);
        }

        $user = null;
        if ($isEmail) {
            $user = \App\Models\User::where('email', $identifier)->first();
        } else {
            $user = \App\Models\User::whereRaw("REPLACE(REPLACE(REPLACE(mobile, ' ', ''), '+', ''), '-', '') = ?", [$identifier])->first();
        }

        if (! $user) {
            return back()->withErrors(['email' => 'No user found with that email or mobile.']);
        }

        Auth::login($user);
        $request->session()->regenerate();

        // Redirect by role/superadmin
        if ($user->email === 'superadmin@gmail.com') {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        if (isset($user->role)) {
            switch ($user->role) {
                case 'admin':
                case 'manager':
                    return redirect()->intended(route('dashboard', absolute: false));
                case 'user':
                default:
                    return redirect()->intended(route('user.dashboard', absolute: false));
            }
        }

        return redirect()->intended(route('user.dashboard', absolute: false));
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();
        $user = Auth::user();
        if ($user) {
            // Superadmin shortcut by email
            if ($user->email === 'superadmin@gmail.com') {
                return redirect()->intended(route('dashboard', absolute: false));
            }

            // Role-based redirection
            if (isset($user->role)) {
                switch ($user->role) {
                    case 'admin':
                    case 'manager':
                        return redirect()->intended(route('dashboard', absolute: false));
                    case 'user':
                    default:
                        return redirect()->intended(route('user.dashboard', absolute: false));
                }
            }
        }

        return redirect()->intended(route('user.dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
