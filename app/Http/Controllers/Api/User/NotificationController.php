<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use App\Services\NotificationService;

class NotificationController extends Controller
{
    // GET /api/notifications
    public function index(Request $request): JsonResponse
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->when(isset($request->is_read), fn($q) => $q->where('is_read', $request->boolean('is_read')))
            ->when($request->type, fn($q) => $q->where('type', $request->type))
            ->latest()
            ->paginate($request->per_page ?? 20);

        return response()->json(['success' => true, 'data' => $notifications]);
    }

    // GET /api/notifications/unread-count
    public function unreadCount(): JsonResponse
    {
        $count = Notification::where('user_id', auth()->id())
                             ->where('is_read', false)
                             ->count();

        return response()->json(['success' => true, 'data' => ['unread_count' => $count]]);
    }

    // PUT /api/notifications/{id}/read
    public function markAsRead(int $id): JsonResponse
    {
        $notification = Notification::where('user_id', auth()->id())->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['success' => true, 'message' => 'Notifikasi ditandai sudah dibaca.']);
    }

    // PUT /api/notifications/read-all
    public function markAllAsRead(): JsonResponse
    {
        Notification::where('user_id', auth()->id())
                    ->where('is_read', false)
                    ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json(['success' => true, 'message' => 'Semua notifikasi ditandai sudah dibaca.']);
    }

    // DELETE /api/notifications/{id}
    public function destroy(int $id): JsonResponse
    {
        $notification = Notification::where('user_id', auth()->id())->findOrFail($id);
        $notification->delete();

        return response()->json(['success' => true, 'message' => 'Notifikasi berhasil dihapus.']);
    }

    // ── Static helper untuk kirim notifikasi dari controller lain ──
    // Simpan ke tabel notifications DAN kirim push FCM ke HP (lewat NotificationService).
    // Contoh: NotificationController::send($userId, 'request_approved', 'Permintaan Disetujui', 'Permintaan #1 disetujui.')
    public static function send(int $userId, string $type, string $title, string $body, array $data = []): void
    {
        $user = User::find($userId);
        if (! $user) {
            return;
        }

        NotificationService::send($user, $type, $title, $body, $data);
    }

    // ── Kirim ke semua user aktif dengan role tertentu (DB + push FCM) ──
    // Contoh: NotificationController::sendToRole('admin', 'request_created', 'Request Baru', 'Ada request baru.')
    public static function sendToRole(string $role, string $type, string $title, string $body, array $data = []): void
    {
        $users = User::where('role', $role)->where('is_active', true)->get();

        foreach ($users as $user) {
            NotificationService::send($user, $type, $title, $body, $data);
        }
    }
}
