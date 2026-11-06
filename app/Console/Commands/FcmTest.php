<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\FcmService;
use Illuminate\Console\Command;

/**
 * php artisan fcm:test 1
 */
class FcmTest extends Command
{
    protected $signature   = 'fcm:test {user_id : ID user tujuan}';
    protected $description = 'Kirim notifikasi tes ke satu user';

    public function handle(FcmService $fcm): int
    {
        if (! $fcm->isConfigured()) {
            $this->error('File service account tidak ditemukan: ' . config('fcm.credentials'));
            return self::FAILURE;
        }

        $user = User::find($this->argument('user_id'));
        if (! $user) {
            $this->error('User tidak ditemukan.');
            return self::FAILURE;
        }

        if (! $user->fcm_token) {
            $this->error("{$user->name} belum punya fcm_token. Login dulu di aplikasi.");
            return self::FAILURE;
        }

        $r = $fcm->sendToToken($user->fcm_token, '🔔 Tes notifikasi', 'Kalau ini muncul, FCM sudah jalan.', [
            'action' => 'test',
        ]);

        if ($r['ok']) {
            $this->info("Terkirim ke {$user->name}. Cek HP-nya.");
            return self::SUCCESS;
        }

        $this->error('Gagal: ' . $r['error']);
        if ($r['invalid_token']) {
            $this->line('Token tidak berlaku lagi. Logout lalu login ulang di aplikasi.');
        }
        return self::FAILURE;
    }
}
