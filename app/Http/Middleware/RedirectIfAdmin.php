<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectIfAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if ($user) {
            // Prefer explicit role field if available
            if (isset($user->role) && $user->role === 'admin') {
                return redirect()->route('dashboard');
            }
            // Fallback: keep existing superadmin email check
            if (! isset($user->role) && isset($user->email) && $user->email === 'superadmin@gmail.com') {
                return redirect()->route('dashboard');
            }
        }

        return $next($request);
    }
}
