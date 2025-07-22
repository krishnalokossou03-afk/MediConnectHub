<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.admin-login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::guard('admin')->user();
            if ($user->email === 'lucmariolokossou@gmail.com') {
                return redirect()->route('admin.dashboard');
            } else {
                Auth::guard('admin')->logout();
                return back()->withErrors(['email' => 'Accès réservé à l\'administrateur.']);
            }
        }

        return back()->withErrors([
            'email' => 'Identifiants invalides.',
        ]);
    }

    public function quickLogin()
    {
        $adminEmail = 'lucmariolokossou@gmail.com';
        $admin = \App\Models\User::where('email', $adminEmail)->first();
        if ($admin) {
            Auth::guard('admin')->login($admin);
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->route('admin.login')->with('error', 'Admin introuvable.');
        }
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('home');
    }
} 