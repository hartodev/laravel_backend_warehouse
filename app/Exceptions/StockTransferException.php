<?php

namespace App\Exceptions;

/**
 * Dilempar oleh StockTransferService ketika sebuah aksi melanggar business rule
 * (status tidak sesuai, bukan pemilik gudang, dsb).
 *
 * Api\Admin\StockTransferController menangkap ini lalu mengembalikan JSON
 * dengan status() sebagai HTTP code.
 * Web\Admin & Web\Superadmin\StockTransferController menangkap ini lalu
 * redirect back dengan flash 'error' — status() diabaikan karena web pakai redirect.
 */
class StockTransferException extends \RuntimeException
{
    protected int $status;

    public function __construct(string $message, int $status = 422)
    {
        parent::__construct($message);
        $this->status = $status;
    }

    public function status(): int
    {
        return $this->status;
    }
}
