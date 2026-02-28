<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class RoleCheckController extends Controller
{
    public function checkRole(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $email = $request->input('email');
        $user = User::where('email', $email)->first();
        if ($user) {
            return response()->json(['role' => $user->role ?? 'user']);
        }
        // if user not found, default to 'user' flow
        return response()->json(['role' => 'user']);
    }
}
