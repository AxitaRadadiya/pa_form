<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserLoginController extends Controller
{
    public function loginWithMobile(Request $request)
    {
        $data = $request->validate([
            'email' => 'nullable|email|required_without:mobile',
            'mobile' => 'nullable|string|required_without:email'
        ]);

        // Prefer mobile lookup if mobile provided, otherwise use email
        if (!empty($data['mobile'])) {
            $user = User::where('mobile', $data['mobile'])->first();
        } else {
            $user = User::where('email', $data['email'])->first();
        }

        if (!$user) {
            return redirect()->back()->with('error', 'Invalid credentials.');
        }

        Auth::login($user);
        return redirect()->intended('/user');
    }
}
