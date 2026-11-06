<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\FcmService;
use Illuminate\Console\Command;

/**
 * Diagnosa cepat kesiapan FCM (tanpa mengirim apa pun).
 *
 *   php artisan fcm:check        → cek file kunci + daftar user yang punya token
 *   php artisan fcm:check 2      → plus cek user dengan ID 2
 */
class FcmCheck extends Command
{
    protected $signature   = 'fcm:check {user_id? : ID user yang ingin dicek (opsional)}';
    protected $description = 'Cek kesiapan FCM: file kunci, konfigurasi, dan token HP user';

    public function handle(FcmService $fcm): int
    {
        $ok   = true;
        $path = (string) config('fcm.credentials');

        $this->line('File kunci : ' . $path);

        if (! is_file($path)) {
            $this->error('✗ File kunci TIDAK ditemukan. Jalankan scripts\\setup-fcm.ps1 atau taruh file di storage/app/firebase-credentials.json');
            $ok = false;
        } else {
            $json = json_decode((string) file_get_contents($path), true);

            if (! is_array($json) || empty($json['client_email']) || empty($json['private_key']) || empty($json['project_id'])) {
                $this->error('✗ File kunci ada, tetapi isinya tidak valid (bukan service account Firebase).');
                $ok = false;
            } else {
                $this->info('✓ File kunci valid. Project Firebase: ' . $json['project_id']);
                $this->line('  Service account: ' . $json['client_email']);
            }
        }

        if ($ok && ! $fcm->isConfigured()) {
            $this->error('✗ FcmService menganggap FCM belum siap. Jalankan: php artisan config:clear');
            $ok = false;
        }

        $this->line('Channel    : ' . config('fcm.android_channel_id'));

        $users = User::whereNotNull('fcm_token')->get(['id', 'name', 'email', 'role']);
        $this->newLine();
        $this->line('User dengan token HP terdaftar: ' . $users->count());
        foreach ($users as $u) {
            $this->line("  - ID {$u->id} | {$u->email} | {$u->role}");
        }

        if ($users->isEmpty()) {
            $this->warn('! Belum ada token. Login di aplikasi (HP) dan izinkan notifikasi.');
        }

        if ($id = $this->argument('user_id')) {
            $user = User::find($id);
            $this->newLine();

            if (! $user) {
                $this->error("✗ User ID {$id} tidak ditemukan.");
                $ok = false;
            } elseif (! $user->fcm_token) {
                $this->warn("! User ID {$id} ({$user->email}) belum punya token. Login dengan akun ini di HP.");
                $ok = false;
            } else {
                $this->info("✓ User ID {$id} ({$user->email}) siap menerima notifikasi.");
            }
        }

        return $ok ? self::SUCCESS : self::FAILURE;
    }
}
