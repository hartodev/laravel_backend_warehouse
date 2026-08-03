<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class ImageService
{
    const FOLDERS = [
        'users',
        'products',
        'payments',
        'expenses',
        'suppliers',
        'warehouses',
    ];

    /**
     * Upload gambar ke public/images/{folder}/
     * SEBELUM: file lama dihapus SEBELUM file baru berhasil dipindah.
     * SESUDAH: file lama dihapus SETELAH file baru berhasil dipindah,
     *          agar tidak kehilangan gambar jika move() gagal di tengah jalan.
     */
    public static function upload(UploadedFile $file, string $folder, ?string $oldPath = null): string
    {
        $dir = public_path("images/{$folder}");
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $extension = $file->getClientOriginalExtension();
        $filename  = Str::uuid() . '.' . strtolower($extension);

        // Pindahkan file baru dulu — jika ini throw exception, file lama masih utuh.
        $file->move($dir, $filename);

        // Baru hapus file lama setelah file baru dipastikan berhasil tersimpan.
        if ($oldPath) {
            self::delete($oldPath);
        }

        return "images/{$folder}/{$filename}";
    }

    public static function delete(?string $path): void
    {
        if (! $path) return;

        $fullPath = public_path($path);

        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }

    public static function url(?string $path): ?string
    {
        if (! $path) return null;

        return asset($path);
    }

    public static function rules(bool $required = false, int $maxKb = 1024): string
    {
        $prefix = $required ? 'required' : 'nullable';
        return "{$prefix}|image|mimes:jpg,jpeg,png,webp|max:{$maxKb}";
    }

    public static function documentRules(bool $required = false, int $maxKb = 2048): string
    {
        $prefix = $required ? 'required' : 'nullable';
        return "{$prefix}|file|mimes:jpg,jpeg,png,pdf|max:{$maxKb}";
    }
}
