<?php

namespace App\Exceptions;

/**
 * Dilempar oleh BudgetRequestAdminService ketika aksi (approve/reject/tunda)
 * melanggar aturan status. Web\Admin\BudgetRequestController menangkap ini
 * lalu redirect back dengan flash 'error'.
 */
class BudgetRequestException extends \RuntimeException
{
    //
}
