<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserProfile;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    // ── GET /admin/users ──────────────────────────────────────
    public function index(Request $request): View
    {
        $users = User::with('profile')
            ->when($request->role, fn($q) => $q->where('role', $request->role))
            ->when($request->search, fn($q) => $q->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            }))
            ->when($request->filled('is_active'), fn($q) => $q->where('is_active', $request->boolean('is_active')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('Admin.users.index', compact('users'));
    }

    // ── GET /admin/users/create ───────────────────────────────
    public function create(): View
    {
        return view('Admin.users.create');
    }

    // ── GET /admin/users/{user} ───────────────────────────────
    public function show(User $user): View
    {
        $user->load('profile');

        return view('Admin.users.show', compact('user'));
    }

    // ── GET /admin/users/{user}/edit ──────────────────────────
    public function edit(User $user): View
    {
        $user->load('profile');

        return view('Admin.users.edit', compact('user'));
    }

    // ── POST /admin/users ──────────────────────────────────────
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'required|in:super_admin,admin,user',
            'phone'    => 'nullable|string|max:20',
            'address'  => 'nullable|string',
            'photo'    => ImageService::rules(),
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role'      => $request->role,
            'is_active' => true,
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = ImageService::upload($request->file('photo'), 'users');
        }

        UserProfile::create([
            'user_id' => $user->id,
            'phone'   => $request->phone,
            'address' => $request->address,
            'photo'   => $photoPath,
        ]);

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'User berhasil dibuat.');
    }

    // ── PUT /admin/users/{user} ─────────────────────────────────
    public function update(Request $request, User $user): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name'    => 'sometimes|required|string|max:255',
            'email'   => ['sometimes', 'required', 'email', Rule::unique('users')->ignore($user->id)],
            'role'    => 'sometimes|required|in:super_admin,admin,user',
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'photo'   => ImageService::rules(),
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user->update($request->only('name', 'email', 'role'));

        // Update atau buat profile
        $profile = $user->profile ?? UserProfile::create(['user_id' => $user->id]);

        $photoPath = $profile->photo;
        if ($request->hasFile('photo')) {
            // Upload baru, hapus foto lama otomatis
            $photoPath = ImageService::upload($request->file('photo'), 'users', $profile->photo);
        }

        $profile->update([
            'phone'   => $request->phone ?? $profile->phone,
            'address' => $request->address ?? $profile->address,
            'photo'   => $photoPath,
        ]);

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'User berhasil diupdate.');
    }

    // ── DELETE /admin/users/{user} ───────────────────────────────
    public function destroy(User $user): RedirectResponse
    {
        // Tidak boleh hapus diri sendiri
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menghapus akun sendiri.');
        }

        // Hapus foto jika ada
        if ($user->profile?->photo) {
            ImageService::delete($user->profile->photo);
        }

        $user->delete(); // soft delete

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    // ── PATCH /admin/users/{user}/toggle-active ──────────────────
    public function toggleActive(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menonaktifkan akun sendiri.');
        }

        $user->update(['is_active' => ! $user->is_active]);

        return back()->with('success', $user->is_active ? 'User diaktifkan.' : 'User dinonaktifkan.');
    }

    // ── PATCH /admin/users/{user}/reset-password ─────────────────
    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $user->update(['password' => Hash::make($request->password)]);

        // Hapus semua token agar user login ulang
        $user->tokens()->delete();

        return back()->with('success', 'Password berhasil direset. User harus login ulang.');
    }
}
