<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserProfile;
use App\Services\ImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
      // ── GET /api/users ───────────────────────────────────────
    public function index(Request $request): JsonResponse
    {
        $query = User::with('profile')
            ->when($request->role, fn($q) => $q->where('role', $request->role))
            ->when($request->search, fn($q) => $q->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            }))
            ->when(isset($request->is_active), fn($q) => $q->where('is_active', $request->boolean('is_active')))
            ->latest()
            ->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data'    => $query->through(fn($user) => AuthController::formatUser($user)),
        ]);
    }

    // ── GET /api/users/{user} ────────────────────────────────
    public function show(User $user): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => AuthController::formatUser($user->load('profile')),
        ]);
    }

    // ── POST /api/users ──────────────────────────────────────
    public function store(Request $request): JsonResponse
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
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors'  => $validator->errors(),
            ], 422);
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

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dibuat.',
            'data'    => AuthController::formatUser($user->load('profile')),
        ], 201);
    }

    // ── PUT /api/users/{user} ────────────────────────────────
    public function update(Request $request, User $user): JsonResponse
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
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors'  => $validator->errors(),
            ], 422);
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

        return response()->json([
            'success' => true,
            'message' => 'User berhasil diupdate.',
            'data'    => AuthController::formatUser($user->fresh('profile')),
        ]);
    }

    // ── DELETE /api/users/{user} ─────────────────────────────
    public function destroy(User $user): JsonResponse
    {
        // Tidak boleh hapus diri sendiri
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus akun sendiri.',
            ], 403);
        }

        // Hapus foto jika ada
        if ($user->profile?->photo) {
            ImageService::delete($user->profile->photo);
        }

        $user->delete(); // soft delete

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dihapus.',
        ]);
    }

    // ── PATCH /api/users/{user}/toggle-active ────────────────
    public function toggleActive(User $user): JsonResponse
    {
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menonaktifkan akun sendiri.',
            ], 403);
        }

        $user->update(['is_active' => ! $user->is_active]);

        return response()->json([
            'success' => true,
            'message' => $user->is_active ? 'User diaktifkan.' : 'User dinonaktifkan.',
            'data'    => ['is_active' => $user->is_active],
        ]);
    }

    // ── PATCH /api/users/{user}/reset-password ───────────────
    public function resetPassword(Request $request, User $user): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user->update(['password' => Hash::make($request->password)]);

        // Hapus semua token agar user login ulang
        $user->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil direset. User harus login ulang.',
        ]);
    }
}


