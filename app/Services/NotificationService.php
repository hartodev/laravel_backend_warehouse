<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    /**
     * Kirim notifikasi ke satu user (simpan ke DB + nanti dirantai ke push FCM).
     */
    public static function send(User $user, string $type, string $title, string $body, array $data = []): Notification
    {
        $notification = Notification::create([
            'user_id' => $user->id,
            'type'    => $type,
            'title'   => $title,
            'body'    => $body,
            'data'    => $data,
        ]);

        // TODO: panggil pengiriman push FCM di sini setelah kreait/firebase-php terpasang
        // self::sendPush($user, $title, $body, $data);

        return $notification;
    }

    /** Kirim ke banyak user sekaligus. */
    public static function sendToMany(iterable $users, string $type, string $title, string $body, array $data = []): void
    {
        foreach ($users as $user) {
            self::send($user, $type, $title, $body, $data);
        }
    }

    /** Kirim ke semua user dengan role tertentu (mis. semua admin). */
    public static function sendToRole(string $role, string $type, string $title, string $body, array $data = []): void
    {
        $users = User::where('role', $role)->where('is_active', true)->get();
        self::sendToMany($users, $type, $title, $body, $data);
    }
}
