<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserProfile;
use App\Services\ImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // ── Login ────────────────────────────────────────────────
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        if (! Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah.',
            ], 401);
        }

        $user = Auth::user();

        if (! $user->is_active) {
            Auth::logout();
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda telah dinonaktifkan. Hubungi administrator.',
            ], 403);
        }

        // Hapus token lama agar tidak menumpuk
        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data'    => [
                'user'       => self::formatUser($user->load('profile')),
                'token'      => $token,
                'token_type' => 'Bearer',
            ],
        ]);
    }

    // ── Register ─────────────────────────────────────────────
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
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
            'role'      => 'user',
            'is_active' => true,
        ]);

        // Buat profil kosong otomatis
        UserProfile::create(['user_id' => $user->id]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil.',
            'data'    => [
                'user'       => self::formatUser($user->load('profile')),
                'token'      => $token,
                'token_type' => 'Bearer',
            ],
        ], 201);
    }

    // ── Logout ───────────────────────────────────────────────
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil.',
        ]);
    }

    // ── Me ───────────────────────────────────────────────────
    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load('profile');

        return response()->json([
            'success' => true,
            'data'    => self::formatUser($user),
        ]);
    }

    // ── Helper: format response user ─────────────────────────
    // Static agar bisa dipakai controller lain
    // contoh: AuthController::formatUser($user)
    public static function formatUser(User $user): array
    {
        return [
            'id'        => $user->id,
            'name'      => $user->name,
            'email'     => $user->email,
            'role'      => $user->role,
            'is_active' => $user->is_active,
            'profile'   => $user->profile ? [
                'phone'   => $user->profile->phone,
                'address' => $user->profile->address,
                // Foto dari public/images/users/ — pakai ImageService::url()
                'photo'   => ImageService::url($user->profile->photo),
            ] : null,
            'created_at' => $user->created_at?->toDateTimeString(),
        ];
    }
}
