<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

/**
 * ImageService
 * Semua proses upload, hapus, dan akses foto disimpan di public/images/
 *
 * Contoh pemakaian di controller:
 *   use App\Services\ImageService;
 *
 *   // Upload
 *   $path = ImageService::upload($request->file('photo'), 'users');
 *   // → disimpan di public/images/users/uuid.jpg
 *   // → $path berisi "images/users/uuid.jpg"
 *
 *   // Hapus
 *   ImageService::delete($user->profile->photo);
 *
 *   // URL lengkap
 *   ImageService::url($user->profile->photo);
 *   // → http://domain.com/images/users/uuid.jpg
 */
class ImageService
{
    /**
     * Folder yang tersedia.
     * Tambahkan folder baru di sini jika diperlukan.
     */
    const FOLDERS = [
        'users',       // foto profil user
        'products',    // foto produk
        'payments',    // bukti transfer / pembayaran
        'expenses',    // lampiran expense report
        'suppliers',   // foto / dokumen supplier
        'warehouses',  // foto gudang
    ];

    /**
     * Upload gambar ke public/images/{folder}/
     *
     * @param  UploadedFile  $file    File dari $request->file('field')
     * @param  string        $folder  Nama folder tujuan (lihat FOLDERS di atas)
     * @param  string|null   $oldPath Path lama untuk dihapus otomatis (opsional)
     * @return string                 Path relatif: "images/users/uuid.jpg"
     */
    public static function upload(UploadedFile $file, string $folder, ?string $oldPath = null): string
    {
        // Hapus file lama jika ada
        if ($oldPath) {
            self::delete($oldPath);
        }

        // Pastikan folder ada
        $dir = public_path("images/{$folder}");
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Generate nama file unik
        $extension = $file->getClientOriginalExtension();
        $filename  = Str::uuid() . '.' . strtolower($extension);

        // Pindahkan file ke public/images/{folder}/
        $file->move($dir, $filename);

        return "images/{$folder}/{$filename}";
    }

    /**
     * Hapus file dari public/
     *
     * @param  string|null  $path  Path relatif: "images/users/uuid.jpg"
     */
    public static function delete(?string $path): void
    {
        if (! $path) return;

        $fullPath = public_path($path);

        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }

    /**
     * Dapatkan URL lengkap dari path relatif.
     * Return null jika path kosong.
     *
     * @param  string|null  $path  Path relatif: "images/users/uuid.jpg"
     * @return string|null
     */
    public static function url(?string $path): ?string
    {
        if (! $path) return null;

        return asset($path);
    }

    /**
     * Validasi rule untuk input file gambar.
     * Pakai di Validator atau FormRequest.
     *
     * Contoh:
     *   'photo' => ImageService::rules()
     *   'photo' => ImageService::rules(required: true, maxKb: 2048)
     */
    public static function rules(bool $required = false, int $maxKb = 1024): string
    {
        $prefix = $required ? 'required' : 'nullable';
        return "{$prefix}|image|mimes:jpg,jpeg,png,webp|max:{$maxKb}";
    }

    /**
     * Validasi rule untuk file dokumen (PDF, dll).
     * Dipakai untuk lampiran expense report, bukti transfer, dll.
     */
    public static function documentRules(bool $required = false, int $maxKb = 2048): string
    {
        $prefix = $required ? 'required' : 'nullable';
        return "{$prefix}|file|mimes:jpg,jpeg,png,pdf|max:{$maxKb}";
    }
}