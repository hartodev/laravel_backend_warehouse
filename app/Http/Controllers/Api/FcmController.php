<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FcmService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FcmController extends Controller
{
    public function __construct(private FcmService $fcm) {}

    /**
     * Simpan token HP (setelah login / saat token berubah).
     * POST /api/fcm/token   body: { "fcm_token": "..." }
     */
    public function updateToken(Request $request): JsonResponse
    {
        $request->validate([
            'fcm_token' => 'required|string|max:500',
        ], [
            'fcm_token.required' => 'Token FCM wajib dikirim.',
        ]);

        $user  = $request->user();
        $token = trim($request->fcm_token);

        // 1 HP = 1 akun: lepas token ini dari akun lain yang pernah login di HP yang sama
        User::where('fcm_token', $token)
            ->where('id', '!=', $user->id)
            ->update(['fcm_token' => null]);

        $user->update(['fcm_token' => $token]);

        return response()->json([
            'success' => true,
            'message' => 'Token notifikasi tersimpan.',
        ]);
    }

    /**
     * Hapus token (saat logout).
     * DELETE /api/fcm/token
     */
    public function deleteToken(Request $request): JsonResponse
    {
        $request->user()->update(['fcm_token' => null]);

        return response()->json([
            'success' => true,
            'message' => 'Token notifikasi dihapus.',
        ]);
    }

    /**
     * Kirim notifikasi tes ke HP sendiri.
     * POST /api/fcm/test
     */
    public function test(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $this->fcm->isConfigured()) {
            return response()->json([
                'success' => false,
                'message' => 'Server belum dikonfigurasi FCM (file service account tidak ada).',
            ], 500);
        }

        if (! $user->fcm_token) {
            return response()->json([
                'success' => false,
                'message' => 'HP ini belum terdaftar. Coba logout lalu login lagi.',
            ], 422);
        }

        $ok = $this->fcm->sendToUser(
            $user,
            '🔔 Tes notifikasi',
            'Halo ' . $user->name . ', notifikasi sudah berjalan.',
            ['action' => 'test']
        );

        return response()->json([
            'success' => $ok,
            'message' => $ok
                ? 'Notifikasi tes terkirim. Cek HP Anda.'
                : 'Gagal mengirim. Lihat storage/logs/laravel.log',
        ], $ok ? 200 : 500);
    }
}
