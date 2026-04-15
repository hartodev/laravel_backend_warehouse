<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Controller;
use App\Models\UserProfile;
use App\Services\ImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    // GET /api/profile
    public function show(Request $request): JsonResponse
    {
        $user = $request->user()->load('profile');
 
        return response()->json([
            'success' => true,
            'data'    => AuthController::formatUser($user),
        ]);
    }
 
    // PUT /api/profile
    public function update(Request $request): JsonResponse
    {
        $user = $request->user();
 
        $validator = Validator::make($request->all(), [
            'name'    => 'sometimes|required|string|max:255',
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);
 
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors'  => $validator->errors(),
            ], 422);
        }
 
        if ($request->has('name')) {
            $user->update(['name' => $request->name]);
        }
 
        $profile = $user->profile ?? UserProfile::create(['user_id' => $user->id]);
 
        $profile->update([
            'phone'   => $request->phone ?? $profile->phone,
            'address' => $request->address ?? $profile->address,
        ]);
 
        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diupdate.',
            'data'    => AuthController::formatUser($user->fresh('profile')),
        ]);
    }
 
    // POST /api/profile/photo
    public function updatePhoto(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'photo' => ImageService::rules(required: true),
        ]);
 
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors'  => $validator->errors(),
            ], 422);
        }
 
        $user    = $request->user();
        $profile = $user->profile ?? UserProfile::create(['user_id' => $user->id]);
 
        // Upload foto baru, hapus yang lama otomatis
        $photoPath = ImageService::upload(
            $request->file('photo'),
            'users',
            $profile->photo
        );
 
        $profile->update(['photo' => $photoPath]);
 
        return response()->json([
            'success' => true,
            'message' => 'Foto profil berhasil diupdate.',
            'data'    => [
                'photo' => ImageService::url($photoPath),
            ],
        ]);
    }
 
    // PUT /api/profile/change-password
    public function changePassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password'         => 'required|string|min:8|confirmed',
        ]);
 
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors'  => $validator->errors(),
            ], 422);
        }
 
        $user = $request->user();
 
        if (! Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password saat ini tidak sesuai.',
            ], 422);
        }
 
        $user->update(['password' => Hash::make($request->password)]);
 
        // Hapus semua token lain agar sesi lain logout
        $user->tokens()->where('id', '!=', $request->user()->currentAccessToken()->id)->delete();
 
        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diubah.',
        ]);
    }
}
