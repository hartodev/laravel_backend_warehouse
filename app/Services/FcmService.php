<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Kirim push notification lewat FCM HTTP v1 (tanpa package tambahan).
 *
 * app(FcmService::class)->sendToUser($user, 'Judul', 'Isi', ['action' => 'notification']);
 */
class FcmService
{
    private const SCOPE = 'https://www.googleapis.com/auth/firebase.messaging';

    private ?array $credentials = null;

    public function isConfigured(): bool
    {
        return File::exists((string) config('fcm.credentials'));
    }

    private function credentials(): array
    {
        if ($this->credentials !== null) {
            return $this->credentials;
        }

        $path = (string) config('fcm.credentials');
        if (! File::exists($path)) {
            throw new \RuntimeException("File service account FCM tidak ditemukan: {$path}");
        }

        $json = json_decode(File::get($path), true);
        if (! is_array($json) || empty($json['client_email']) || empty($json['private_key'])) {
            throw new \RuntimeException('File service account FCM tidak valid.');
        }

        return $this->credentials = $json;
    }

    private function projectId(): string
    {
        return config('fcm.project_id') ?: $this->credentials()['project_id'];
    }

    private function base64Url(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /** Token akses Google (berlaku 1 jam, disimpan di cache 55 menit) */
    private function accessToken(): string
    {
        return Cache::remember('fcm_access_token', 55 * 60, function () {
            $cred     = $this->credentials();
            $tokenUri = $cred['token_uri'] ?? 'https://oauth2.googleapis.com/token';
            $now      = time();

            $header = $this->base64Url(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
            $claims = $this->base64Url(json_encode([
                'iss'   => $cred['client_email'],
                'scope' => self::SCOPE,
                'aud'   => $tokenUri,
                'iat'   => $now,
                'exp'   => $now + 3600,
            ]));

            $unsigned  = $header . '.' . $claims;
            $signature = '';
            if (! openssl_sign($unsigned, $signature, $cred['private_key'], OPENSSL_ALGO_SHA256)) {
                throw new \RuntimeException('Gagal menandatangani JWT FCM (cek private_key).');
            }

            $res = Http::asForm()->timeout(15)->post($tokenUri, [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion'  => $unsigned . '.' . $this->base64Url($signature),
            ]);

            if (! $res->successful() || ! $res->json('access_token')) {
                throw new \RuntimeException('Gagal ambil access token FCM: ' . $res->body());
            }

            return $res->json('access_token');
        });
    }

    /** @return array{ok:bool, invalid_token:bool, error:?string} */
    public function sendToToken(string $token, string $title, string $body, array $data = []): array
    {
        // FCM v1: semua nilai "data" wajib string
        $data = collect($data)->map(fn ($v) => is_scalar($v) ? (string) $v : json_encode($v))->all();

        $payload = [
            'message' => [
                'token'        => $token,
                'notification' => ['title' => $title, 'body' => $body],
                'data'         => (object) $data,
                'android'      => [
                    'priority'     => 'HIGH',
                    'notification' => [
                        'channel_id' => config('fcm.android_channel_id'),
                        'sound'      => 'default',
                    ],
                ],
                'apns' => ['payload' => ['aps' => ['sound' => 'default']]],
            ],
        ];

        try {
            $res = Http::withToken($this->accessToken())
                ->timeout(15)
                ->post("https://fcm.googleapis.com/v1/projects/{$this->projectId()}/messages:send", $payload);
        } catch (\Throwable $e) {
            Log::warning('FCM error', ['error' => $e->getMessage()]);
            return ['ok' => false, 'invalid_token' => false, 'error' => $e->getMessage()];
        }

        if ($res->successful()) {
            return ['ok' => true, 'invalid_token' => false, 'error' => null];
        }

        $code    = $res->json('error.details.0.errorCode') ?? $res->json('error.status');
        $invalid = in_array($code, ['UNREGISTERED', 'INVALID_ARGUMENT'], true) || $res->status() === 404;

        if ($res->status() === 401) {
            Cache::forget('fcm_access_token');
        }

        Log::warning('FCM gagal kirim', ['status' => $res->status(), 'code' => $code, 'body' => $res->body()]);

        return ['ok' => false, 'invalid_token' => $invalid, 'error' => $code ?: $res->body()];
    }

    /** Kirim ke 1 user. Token yang tidak berlaku otomatis dikosongkan. */
    public function sendToUser(User $user, string $title, string $body, array $data = []): bool
    {
        if (! $this->isConfigured()) {
            Log::warning('FCM belum dikonfigurasi', ['path' => config('fcm.credentials')]);
            return false;
        }

        if (! $user->fcm_token) {
            return false;
        }

        $r = $this->sendToToken($user->fcm_token, $title, $body, $data);

        if (! $r['ok'] && $r['invalid_token']) {
            User::where('fcm_token', $user->fcm_token)->update(['fcm_token' => null]);
        }

        return $r['ok'];
    }

    /** Kirim ke banyak user, mengembalikan jumlah yang terkirim */
    public function sendToUsers(iterable $users, string $title, string $body, array $data = []): int
    {
        $sent = 0;
        foreach ($users as $user) {
            if ($this->sendToUser($user, $title, $body, $data)) {
                $sent++;
            }
        }
        return $sent;
    }
}
