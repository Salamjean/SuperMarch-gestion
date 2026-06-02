<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectByProfile(Auth::user());
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login_code' => ['required', 'string'],
            'password'   => ['required'],
        ]);

        if (!Auth::attempt(['login_code' => $credentials['login_code'], 'password' => $credentials['password']], $request->boolean('remember'))) {
            return back()->withErrors([
                'login_code' => 'Code ID ou mot de passe incorrect.',
            ])->onlyInput('login_code');
        }

        $request->session()->regenerate();

        return $this->redirectByProfile(Auth::user());
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    private function redirectByProfile($user)
    {
        if ($user->role === 'employee' && $user->position === 'magasinier') {
            return redirect()->route('magasinier.dashboard');
        }

        return match ($user->role) {
            'admin'    => redirect()->route('admin.dashboard'),
            'employee' => redirect()->route('employee.dashboard'),
            'magasinier' => redirect()->route('magasinier.dashboard'),
            default    => redirect()->route('home'),
        };
    }
}
