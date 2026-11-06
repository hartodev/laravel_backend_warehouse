<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Console\Command;

/**
 * Simulasi notifikasi bisnis lewat jalur ASLI (NotificationService):
 * tersimpan di tabel notifications DAN dikirim sebagai push ke HP.
 * Tidak perlu akun kedua / panel web untuk memicunya.
 *
 *   php artisan notif:demo 2 --list
 *   php artisan notif:demo 2 --type=rab
 *   php artisan notif:demo 2 --all
 */
class NotifDemo extends Command
{
    protected $signature = 'notif:demo
                            {user_id : ID user penerima (akun yang sedang login di HP)}
                            {--type=rab : request | rab | po | produk | transfer | opname}
                            {--all : kirim semua jenis sekaligus}
                            {--list : tampilkan jenis yang tersedia}';

    protected $description = 'Kirim notifikasi contoh (DB + push FCM) lewat NotificationService';

    /** @return array<string, array{0:string,1:string,2:string,3:array}> */
    private function samples(): array
    {
        return [
            'request'  => ['request_created',  '[TES] Request Baru Masuk',          'Budi mengajukan permintaan baru #REQ-TES-001.', ['request_id' => 0]],
            'rab'      => ['budget_request',   '[TES] RAB Baru Masuk',              'RAB RAB-TES-001 menunggu review admin.',        ['budget_request_id' => 0]],
            'po'       => ['purchase_order',   '[TES] PO Menunggu Persetujuan',     'PO PO-TES-001 menunggu persetujuan Anda.',      ['purchase_order_id' => 0]],
            'produk'   => ['product_submission', '[TES] Pengajuan Produk Baru',     'Pengajuan produk "Contoh Produk" perlu direview.', ['product_submission_id' => 0]],
            'transfer' => ['stock_transfer',   '[TES] Transfer Menunggu Persetujuan', 'Transfer TRF-TES-001 menunggu persetujuan.',  ['stock_transfer_id' => 0]],
            'opname'   => ['stock_opname',     '[TES] Opname Menunggu Persetujuan', 'Stok opname OPN-TES-001 menunggu persetujuan Anda.', ['stock_opname_id' => 0]],
        ];
    }

    public function handle(): int
    {
        $samples = $this->samples();

        if ($this->option('list')) {
            $this->line('Jenis yang tersedia: ' . implode(', ', array_keys($samples)));

            return self::SUCCESS;
        }

        $user = User::find($this->argument('user_id'));
        if (! $user) {
            $this->error('User tidak ditemukan.');

            return self::FAILURE;
        }

        if (! $user->fcm_token) {
            $this->warn("{$user->email} belum punya fcm_token: notifikasi hanya masuk ke lonceng, push tidak terkirim.");
        }

        $types = $this->option('all') ? array_keys($samples) : [(string) $this->option('type')];

        foreach ($types as $key) {
            if (! isset($samples[$key])) {
                $this->error("Jenis \"{$key}\" tidak dikenal. Pilihan: " . implode(', ', array_keys($samples)));

                return self::FAILURE;
            }

            [$type, $title, $body, $data] = $samples[$key];
            $n = NotificationService::send($user, $type, $title, $body, $data);
            $this->info("✓ [{$key}] tersimpan (notifikasi #{$n->id}) untuk {$user->email}");
        }

        $this->line('Push dikirim saat command selesai. Cek HP; kalau tidak muncul: php artisan fcm:test ' . $user->id);

        return self::SUCCESS;
    }
}
