<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

class AuthWebController extends Controller
{
    /**
     * Tentukan halaman tujuan setelah login, berdasarkan role user.
     *
     * FIX: sebelumnya semua method (showLogin, login, showRegister, register)
     * hardcode redirect ke 'superadmin.dashboard', jadi user role 'admin'
     * pun ikut diarahkan ke /superadmin dan kena 403. Sekarang tujuan
     * ditentukan dari role user yang sedang login.
     */
    private function redirectRouteForRole(?string $role): string
    {
        return match ($role) {
            'super_admin'      => route('superadmin.dashboard'),
            'admin', 'partner' => route('admin.products.index'), // halaman admin yang sudah ada; ganti kalau mau landing page lain
            default            => route('home'),
        };
    }

    /**
     * Tampilkan halaman login.
     * Selalu munculin form login apapun kondisinya — tidak auto-redirect
     * walau user masih ada session aktif, supaya tombol "Login" di
     * landing page selalu konsisten membawa ke /login.
     */
    public function showLogin()
    {
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

        $user = Auth::user();

        Log::info('Login berhasil', [
            'user_id'  => $user->id,
            'email'    => $user->email,
            'role'     => $user->role,
            'redirect' => $this->redirectRouteForRole($user->role),
        ]);

        // Optional: batasi hanya role tertentu yang boleh masuk web panel
        // if (! in_array($user->role, ['super_admin', 'admin', 'partner'])) {
        //     Auth::logout();
        //     throw ValidationException::withMessages([
        //         'email' => __('Anda tidak memiliki akses ke panel ini.'),
        //     ]);
        // }

        return redirect()->intended($this->redirectRouteForRole($user->role));
    }

    /**
     * Tampilkan halaman register.
     */
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->to($this->redirectRouteForRole(Auth::user()->role));
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

        return redirect()->to($this->redirectRouteForRole($user->role));
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