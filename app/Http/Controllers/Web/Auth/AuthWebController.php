<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

class AuthWebController extends Controller
{
    /**
     * Tampilkan halaman login.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('superadmin.dashboard');
        }

        return view('auth.login');
    }

    /**
     * Proses login.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => __('Email atau password yang Anda masukkan salah.'),
            ]);
        }

        $request->session()->regenerate();

        // Optional: batasi hanya role tertentu (misal superadmin) yang boleh masuk web panel
        // $user = Auth::user();
        // if ($user->role !== 'superadmin') {
        //     Auth::logout();
        //     throw ValidationException::withMessages([
        //         'email' => __('Anda tidak memiliki akses ke panel ini.'),
        //     ]);
        // }

        return redirect()->intended(route('superadmin.dashboard'));
    }

    /**
     * Tampilkan halaman register.
     */
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('superadmin.dashboard');
        }

        return view('auth.register');
    }

    /**
     * Proses register.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            // 'role'  => 'staff', // sesuaikan jika ada kolom role
        ]);

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->route('superadmin.dashboard');
    }

    /**
     * Proses logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
