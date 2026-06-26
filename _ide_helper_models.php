<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $activity
 * @property string|null $module
 * @property int|null $subject_id
 * @property string|null $description
 * @property array|null $old_values
 * @property array|null $new_values
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog whereActivity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog whereModule($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog whereNewValues($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog whereOldValues($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog whereSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog whereUserId($value)
 */
	class ActivityLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int|null $warehouse_id
 * @property int|null $product_id
 * @property string $barcode_value
 * @property string $scan_type
 * @property bool $is_found
 * @property string|null $device_info
 * @property \Illuminate\Support\Carbon $scanned_at
 * @property-read \App\Models\Product|null $product
 * @property-read \App\Models\User $user
 * @property-read \App\Models\Warehouse|null $warehouse
 * @method static \Illuminate\Database\Eloquent\Builder|BarcodeLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BarcodeLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BarcodeLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|BarcodeLog whereBarcodeValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BarcodeLog whereDeviceInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BarcodeLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BarcodeLog whereIsFound($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BarcodeLog whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BarcodeLog whereScanType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BarcodeLog whereScannedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BarcodeLog whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BarcodeLog whereWarehouseId($value)
 */
	class BarcodeLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nomor_form
 * @property int $user_id
 * @property string $divisi
 * @property \Illuminate\Support\Carbon $tanggal_pengajuan
 * @property string $jenis
 * @property string|null $kode_akun
 * @property string|null $nama_akun
 * @property string|null $alasan_luar_rab
 * @property string $urgensi
 * @property string|null $dampak_jika_tidak
 * @property string|null $sumber_dana
 * @property string $total_estimasi
 * @property string|null $keterangan
 * @property string $status
 * @property int|null $branch_manager_id
 * @property \Illuminate\Support\Carbon|null $branch_manager_at
 * @property string|null $catatan_branch_manager
 * @property int|null $finance_id
 * @property \Illuminate\Support\Carbon|null $finance_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $branchManager
 * @property-read \App\Models\ExpenseReport|null $expenseReport
 * @property-read \App\Models\User|null $finance
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Payment> $payments
 * @property-read int|null $payments_count
 * @property-read \App\Models\BudgetRevision|null $revision
 * @property-read \App\Models\User $user
 * @property-read \App\Models\BudgetVerification|null $verification
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRequest onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRequest whereAlasanLuarRab($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRequest whereBranchManagerAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRequest whereBranchManagerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRequest whereCatatanBranchManager($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRequest whereDampakJikaTidak($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRequest whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRequest whereDivisi($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRequest whereFinanceAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRequest whereFinanceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRequest whereJenis($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRequest whereKeterangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRequest whereKodeAkun($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRequest whereNamaAkun($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRequest whereNomorForm($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRequest whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRequest whereSumberDana($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRequest whereTanggalPengajuan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRequest whereTotalEstimasi($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRequest whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRequest whereUrgensi($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRequest whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRequest withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRequest withoutTrashed()
 */
	class BudgetRequest extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $budget_request_id
 * @property string $nama_item
 * @property string|null $qty
 * @property string|null $satuan
 * @property string $estimasi_biaya
 * @property string $total
 * @property string|null $keterangan
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\BudgetRequest $budgetRequest
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRequestItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRequestItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRequestItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRequestItem whereBudgetRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRequestItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRequestItem whereEstimasiBiaya($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRequestItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRequestItem whereKeterangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRequestItem whereNamaItem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRequestItem whereQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRequestItem whereSatuan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRequestItem whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRequestItem whereUpdatedAt($value)
 */
	class BudgetRequestItem extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $budget_request_id
 * @property int|null $expense_report_id
 * @property int $created_by
 * @property int|null $approved_by
 * @property string $akun_terdampak
 * @property string|null $kode_akun
 * @property float $anggaran_awal
 * @property float $realisasi
 * @property string $jenis_perubahan
 * @property float $nominal_perubahan
 * @property string $anggaran_baru
 * @property string $alasan_revisi
 * @property string $status
 * @property string|null $catatan_approver
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $approvedBy
 * @property-read \App\Models\BudgetRequest|null $budgetRequest
 * @property-read \App\Models\User $createdBy
 * @property-read \App\Models\ExpenseReport|null $expenseReport
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRevision newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRevision newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRevision query()
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRevision whereAkunTerdampak($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRevision whereAlasanRevisi($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRevision whereAnggaranAwal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRevision whereAnggaranBaru($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRevision whereApprovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRevision whereApprovedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRevision whereBudgetRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRevision whereCatatanApprover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRevision whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRevision whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRevision whereExpenseReportId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRevision whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRevision whereJenisPerubahan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRevision whereKodeAkun($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRevision whereNominalPerubahan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRevision whereRealisasi($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRevision whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetRevision whereUpdatedAt($value)
 */
	class BudgetRevision extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $budget_request_id
 * @property int $finance_id
 * @property bool $doc_form_lengkap
 * @property bool $doc_surat_justifikasi
 * @property bool $doc_estimasi_vendor
 * @property bool $doc_spesifikasi_teknis
 * @property string|null $doc_lainnya
 * @property string|null $cek_anggaran
 * @property string|null $analisa_cashflow
 * @property string $rekomendasi
 * @property string|null $nominal_rekomendasi
 * @property string|null $catatan_finance
 * @property \Illuminate\Support\Carbon $verified_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\BudgetRequest $budgetRequest
 * @property-read \App\Models\User $finance
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetVerification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetVerification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetVerification query()
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetVerification whereAnalisaCashflow($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetVerification whereBudgetRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetVerification whereCatatanFinance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetVerification whereCekAnggaran($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetVerification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetVerification whereDocEstimasiVendor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetVerification whereDocFormLengkap($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetVerification whereDocLainnya($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetVerification whereDocSpesifikasiTeknis($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetVerification whereDocSuratJustifikasi($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetVerification whereFinanceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetVerification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetVerification whereNominalRekomendasi($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetVerification whereRekomendasi($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetVerification whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BudgetVerification whereVerifiedAt($value)
 */
	class BudgetVerification extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $no_bukti
 * @property int|null $payment_id
 * @property int $created_by
 * @property int|null $verified_by
 * @property string $type
 * @property string $pihak
 * @property string $jumlah_uang
 * @property string $terbilang
 * @property string|null $keterangan
 * @property \Illuminate\Support\Carbon $tanggal
 * @property \Illuminate\Support\Carbon|null $verified_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $createdBy
 * @property-read \App\Models\Payment|null $payment
 * @property-read \App\Models\User|null $verifiedBy
 * @method static \Illuminate\Database\Eloquent\Builder|CashBook newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CashBook newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CashBook query()
 * @method static \Illuminate\Database\Eloquent\Builder|CashBook whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashBook whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashBook whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashBook whereJumlahUang($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashBook whereKeterangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashBook whereNoBukti($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashBook wherePaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashBook wherePihak($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashBook whereTanggal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashBook whereTerbilang($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashBook whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashBook whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashBook whereVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashBook whereVerifiedBy($value)
 */
	class CashBook extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $code
 * @property string|null $description
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductSubmission> $productSubmissions
 * @property-read int|null $product_submissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Product> $products
 * @property-read int|null $products_count
 * @method static \Illuminate\Database\Eloquent\Builder|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Category query()
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Category withoutTrashed()
 */
	class Category extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_one_id
 * @property int $user_two_id
 * @property \Illuminate\Support\Carbon|null $last_message_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ChatMessage|null $latestMessage
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ChatMessage> $messages
 * @property-read int|null $messages_count
 * @property-read \App\Models\User|null $receiver
 * @property-read \App\Models\User|null $sender
 * @property-read \App\Models\User $userOne
 * @property-read \App\Models\User $userTwo
 * @method static \Illuminate\Database\Eloquent\Builder|Chat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Chat newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Chat query()
 * @method static \Illuminate\Database\Eloquent\Builder|Chat whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chat whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chat whereLastMessageAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chat whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chat whereUserOneId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chat whereUserTwoId($value)
 */
	class Chat extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $chat_id
 * @property int $sender_id
 * @property string $message
 * @property bool $is_read
 * @property \Illuminate\Support\Carbon|null $read_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Chat $chat
 * @property-read \App\Models\User $sender
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage whereChatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage whereIsRead($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage whereReadAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage whereSenderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage withoutTrashed()
 */
	class ChatMessage extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $budget_request_id
 * @property int $submitted_by
 * @property int|null $verified_by
 * @property string|null $nomor_invoice
 * @property string|null $nama_vendor
 * @property \Illuminate\Support\Carbon $tanggal_transaksi
 * @property float $nominal_realisasi
 * @property float $selisih
 * @property bool $lamp_invoice
 * @property bool $lamp_bukti_transfer
 * @property bool $lamp_kartu_garansi
 * @property bool $lamp_serah_terima
 * @property string|null $lamp_lainnya
 * @property array|null $attachment_files
 * @property string $status
 * @property string|null $catatan
 * @property \Illuminate\Support\Carbon|null $verified_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\BudgetRequest $budgetRequest
 * @property-read \App\Models\BudgetRevision|null $revision
 * @property-read \App\Models\User $submittedBy
 * @property-read \App\Models\User|null $verifiedBy
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseReport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseReport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseReport query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseReport whereAttachmentFiles($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseReport whereBudgetRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseReport whereCatatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseReport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseReport whereLampBuktiTransfer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseReport whereLampInvoice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseReport whereLampKartuGaransi($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseReport whereLampLainnya($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseReport whereLampSerahTerima($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseReport whereNamaVendor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseReport whereNominalRealisasi($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseReport whereNomorInvoice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseReport whereSelisih($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseReport whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseReport whereSubmittedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseReport whereTanggalTransaksi($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseReport whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseReport whereVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseReport whereVerifiedBy($value)
 */
	class ExpenseReport extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string|null $type
 * @property string $title
 * @property string $body
 * @property array|null $data
 * @property bool $is_read
 * @property \Illuminate\Support\Carbon|null $read_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Notification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification query()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereIsRead($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereReadAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereUserId($value)
 */
	class Notification extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $payment_number
 * @property int $created_by
 * @property int|null $verified_by
 * @property int|null $purchase_order_id
 * @property int|null $sales_order_id
 * @property int|null $budget_request_id
 * @property string $payment_type
 * @property string $payment_method
 * @property string|null $nama_pengirim
 * @property string|null $bank_pengirim
 * @property string|null $nama_penerima
 * @property string|null $bank_penerima
 * @property string|null $no_rekening_tujuan
 * @property string|null $diterima_dari
 * @property string $nominal
 * @property string|null $untuk_pembayaran
 * @property string|null $terbilang
 * @property string $status
 * @property string|null $bukti_file
 * @property \Illuminate\Support\Carbon $payment_date
 * @property \Illuminate\Support\Carbon|null $verified_at
 * @property string|null $keterangan
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\BudgetRequest|null $budgetRequest
 * @property-read \App\Models\CashBook|null $cashBook
 * @property-read \App\Models\User $createdBy
 * @property-read \App\Models\PurchaseOrder|null $purchaseOrder
 * @property-read \App\Models\SalesOrder|null $salesOrder
 * @property-read \App\Models\User|null $verifiedBy
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereBankPenerima($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereBankPengirim($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereBudgetRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereBuktiFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereDiterimaDari($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereKeterangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereNamaPenerima($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereNamaPengirim($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereNoRekeningTujuan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereNominal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment wherePaymentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment wherePaymentNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment wherePaymentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment wherePurchaseOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereSalesOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereTerbilang($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereUntukPembayaran($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereVerifiedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment withoutTrashed()
 */
	class Payment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $category_id
 * @property string $name
 * @property string $sku
 * @property string|null $barcode
 * @property string $unit
 * @property int $min_stock
 * @property string $purchase_price
 * @property string $selling_price
 * @property string|null $description
 * @property bool $is_active
 * @property string|null $photo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BarcodeLog> $barcodeLogs
 * @property-read int|null $barcode_logs_count
 * @property-read \App\Models\Category $category
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PurchaseOrderItem> $purchaseOrderItems
 * @property-read int|null $purchase_order_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\RequestItem> $requestItems
 * @property-read int|null $request_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SalesOrderItem> $salesOrderItems
 * @property-read int|null $sales_order_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StockMovement> $stockMovements
 * @property-read int|null $stock_movements_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StockOpnameItem> $stockOpnameItems
 * @property-read int|null $stock_opname_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StockReport> $stockReports
 * @property-read int|null $stock_reports_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StockTransferItem> $stockTransferItems
 * @property-read int|null $stock_transfer_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Stock> $stocks
 * @property-read int|null $stocks_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductSubmission> $submissions
 * @property-read int|null $submissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductUnit> $units
 * @property-read int|null $units_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Warehouse> $warehouses
 * @property-read int|null $warehouses_count
 * @method static \Illuminate\Database\Eloquent\Builder|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereBarcode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereMinStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product wherePurchasePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereSellingPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereSku($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Product withoutTrashed()
 */
	class Product extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $admin_id
 * @property int $category_id
 * @property string $name
 * @property string|null $sku
 * @property string|null $barcode
 * @property string $unit
 * @property int $initial_stock
 * @property int|null $initial_warehouse_id
 * @property string $purchase_price
 * @property string $selling_price
 * @property string|null $description
 * @property string $status
 * @property int|null $approved_by
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property string|null $reject_reason
 * @property int|null $product_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $admin
 * @property-read \App\Models\User|null $approvedBy
 * @property-read \App\Models\Category $category
 * @property-read \App\Models\Warehouse|null $initialWarehouse
 * @property-read \App\Models\Product|null $product
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSubmission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSubmission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSubmission query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSubmission whereAdminId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSubmission whereApprovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSubmission whereApprovedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSubmission whereBarcode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSubmission whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSubmission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSubmission whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSubmission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSubmission whereInitialStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSubmission whereInitialWarehouseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSubmission whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSubmission whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSubmission wherePurchasePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSubmission whereRejectReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSubmission whereSellingPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSubmission whereSku($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSubmission whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSubmission whereUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSubmission whereUpdatedAt($value)
 */
	class ProductSubmission extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $product_id
 * @property string $unit_name
 * @property string $conversion_value
 * @property bool $is_purchase_unit
 * @property bool $is_sell_unit
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder|ProductUnit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductUnit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductUnit query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductUnit whereConversionValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductUnit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductUnit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductUnit whereIsPurchaseUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductUnit whereIsSellUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductUnit whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductUnit whereUnitName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductUnit whereUpdatedAt($value)
 */
	class ProductUnit extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $po_number
 * @property int $supplier_id
 * @property int $warehouse_id
 * @property int $created_by
 * @property int|null $approved_by
 * @property string $status
 * @property \Illuminate\Support\Carbon $order_date
 * @property \Illuminate\Support\Carbon|null $expected_date
 * @property \Illuminate\Support\Carbon|null $received_date
 * @property string $payment_term
 * @property string $subtotal
 * @property string $tax_percent
 * @property string $tax_amount
 * @property string $discount_amount
 * @property string $total_amount
 * @property string|null $notes
 * @property string|null $reject_reason
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $approvedBy
 * @property-read \App\Models\User $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PurchaseOrderItem> $items
 * @property-read int|null $items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Payment> $payments
 * @property-read int|null $payments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StockMovement> $stockMovements
 * @property-read int|null $stock_movements_count
 * @property-read \App\Models\Supplier $supplier
 * @property-read \App\Models\Warehouse $warehouse
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrder newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrder onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrder query()
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrder whereApprovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrder whereApprovedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrder whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrder whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrder whereDiscountAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrder whereExpectedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrder whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrder whereOrderDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrder wherePaymentTerm($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrder wherePoNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrder whereReceivedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrder whereRejectReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrder whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrder whereSubtotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrder whereSupplierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrder whereTaxAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrder whereTaxPercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrder whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrder whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrder whereWarehouseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrder withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrder withoutTrashed()
 */
	class PurchaseOrder extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $purchase_order_id
 * @property int $product_id
 * @property int $quantity_ordered
 * @property int $quantity_received
 * @property string $unit_price
 * @property string $discount_percent
 * @property string $subtotal
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Product $product
 * @property-read \App\Models\PurchaseOrder $purchaseOrder
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderItem whereDiscountPercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderItem whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderItem whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderItem wherePurchaseOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderItem whereQuantityOrdered($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderItem whereQuantityReceived($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderItem whereSubtotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderItem whereUnitPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderItem whereUpdatedAt($value)
 */
	class PurchaseOrderItem extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $request_number
 * @property int $user_id
 * @property int|null $warehouse_id
 * @property string $purpose
 * @property string $status
 * @property int|null $approved_by
 * @property string|null $approved_at
 * @property string|null $completed_at
 * @property string|null $reject_reason
 * @property string|null $note
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $approvedBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\RequestItem> $items
 * @property-read int|null $items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StockMovement> $stockMovements
 * @property-read int|null $stock_movements_count
 * @property-read \App\Models\User $user
 * @property-read \App\Models\Warehouse|null $warehouse
 * @method static \Illuminate\Database\Eloquent\Builder|Request approved()
 * @method static \Illuminate\Database\Eloquent\Builder|Request completed()
 * @method static \Illuminate\Database\Eloquent\Builder|Request newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Request newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Request onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Request pending()
 * @method static \Illuminate\Database\Eloquent\Builder|Request processing()
 * @method static \Illuminate\Database\Eloquent\Builder|Request query()
 * @method static \Illuminate\Database\Eloquent\Builder|Request rejected()
 * @method static \Illuminate\Database\Eloquent\Builder|Request whereApprovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Request whereApprovedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Request whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Request whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Request whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Request whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Request whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Request wherePurpose($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Request whereRejectReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Request whereRequestNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Request whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Request whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Request whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Request whereWarehouseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Request withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Request withoutTrashed()
 */
	class Request extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $request_id
 * @property int|null $product_id
 * @property string|null $external_name
 * @property string|null $external_spec
 * @property string|null $external_link
 * @property string|null $external_price
 * @property int $quantity
 * @property int|null $approved_quantity
 * @property string|null $note
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Product|null $product
 * @property-read \App\Models\Request $request
 * @method static \Illuminate\Database\Eloquent\Builder|RequestItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RequestItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RequestItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|RequestItem whereApprovedQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequestItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequestItem whereExternalLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequestItem whereExternalName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequestItem whereExternalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequestItem whereExternalSpec($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequestItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequestItem whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequestItem whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequestItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequestItem whereRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequestItem whereUpdatedAt($value)
 */
	class RequestItem extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $so_number
 * @property string|null $reference_number
 * @property int $warehouse_id
 * @property int $created_by
 * @property int|null $approved_by
 * @property string $customer_name
 * @property string|null $customer_phone
 * @property string|null $customer_address
 * @property string $payment_method
 * @property string $status
 * @property \Illuminate\Support\Carbon $order_date
 * @property \Illuminate\Support\Carbon|null $due_date
 * @property string|null $notes
 * @property string $subtotal
 * @property string $tax_percent
 * @property string $tax_amount
 * @property string $discount_amount
 * @property string $total_amount
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $approvedBy
 * @property-read \App\Models\User $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SalesOrderItem> $items
 * @property-read int|null $items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Payment> $payments
 * @property-read int|null $payments_count
 * @property-read \App\Models\Warehouse $warehouse
 * @method static \Illuminate\Database\Eloquent\Builder|SalesOrder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SalesOrder newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SalesOrder onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SalesOrder query()
 * @method static \Illuminate\Database\Eloquent\Builder|SalesOrder whereApprovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesOrder whereApprovedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesOrder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesOrder whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesOrder whereCustomerAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesOrder whereCustomerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesOrder whereCustomerPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesOrder whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesOrder whereDiscountAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesOrder whereDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesOrder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesOrder whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesOrder whereOrderDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesOrder wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesOrder whereReferenceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesOrder whereSoNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesOrder whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesOrder whereSubtotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesOrder whereTaxAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesOrder whereTaxPercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesOrder whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesOrder whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesOrder whereWarehouseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesOrder withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SalesOrder withoutTrashed()
 */
	class SalesOrder extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $sales_order_id
 * @property int $product_id
 * @property string|null $description
 * @property int $quantity
 * @property string $unit_price
 * @property string $discount_percent
 * @property string $subtotal
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Product $product
 * @property-read \App\Models\SalesOrder $salesOrder
 * @method static \Illuminate\Database\Eloquent\Builder|SalesOrderItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SalesOrderItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SalesOrderItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|SalesOrderItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesOrderItem whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesOrderItem whereDiscountPercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesOrderItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesOrderItem whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesOrderItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesOrderItem whereSalesOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesOrderItem whereSubtotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesOrderItem whereUnitPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesOrderItem whereUpdatedAt($value)
 */
	class SalesOrderItem extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $warehouse_id
 * @property int $product_id
 * @property int $quantity
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Product $product
 * @property-read \App\Models\Warehouse $warehouse
 * @method static \Illuminate\Database\Eloquent\Builder|Stock newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Stock newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Stock query()
 * @method static \Illuminate\Database\Eloquent\Builder|Stock whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stock whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stock whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stock whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stock whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stock whereWarehouseId($value)
 */
	class Stock extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $product_id
 * @property int $warehouse_id
 * @property int|null $purchase_order_id
 * @property string $type
 * @property int $quantity
 * @property int $quantity_before
 * @property int $quantity_after
 * @property int|null $request_id
 * @property int|null $request_item_id
 * @property string|null $reference_type
 * @property int|null $reference_id
 * @property int $created_by
 * @property string|null $note
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $createdBy
 * @property-read \App\Models\Product $product
 * @property-read \App\Models\PurchaseOrder|null $purchaseOrder
 * @property-read \App\Models\Warehouse $warehouse
 * @method static \Illuminate\Database\Eloquent\Builder|StockMovement adjustment()
 * @method static \Illuminate\Database\Eloquent\Builder|StockMovement byReference(string $type, int $id)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMovement in()
 * @method static \Illuminate\Database\Eloquent\Builder|StockMovement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StockMovement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StockMovement out()
 * @method static \Illuminate\Database\Eloquent\Builder|StockMovement query()
 * @method static \Illuminate\Database\Eloquent\Builder|StockMovement transferIn()
 * @method static \Illuminate\Database\Eloquent\Builder|StockMovement transferOut()
 * @method static \Illuminate\Database\Eloquent\Builder|StockMovement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMovement whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMovement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMovement whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMovement whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMovement wherePurchaseOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMovement whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMovement whereQuantityAfter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMovement whereQuantityBefore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMovement whereReferenceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMovement whereReferenceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMovement whereRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMovement whereRequestItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMovement whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMovement whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMovement whereWarehouseId($value)
 */
	class StockMovement extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $opname_number
 * @property int $warehouse_id
 * @property int $created_by
 * @property int|null $approved_by
 * @property string $status
 * @property string $opname_date
 * @property string $scope
 * @property int|null $category_id
 * @property string|null $started_at
 * @property string|null $completed_at
 * @property string|null $approved_at
 * @property string|null $notes
 * @property string|null $reject_reason
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $approvedBy
 * @property-read \App\Models\Category|null $category
 * @property-read \App\Models\User $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StockOpnameItem> $deviatedItems
 * @property-read int|null $deviated_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StockOpnameItem> $items
 * @property-read int|null $items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StockMovement> $stockMovements
 * @property-read int|null $stock_movements_count
 * @property-read \App\Models\Warehouse $warehouse
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpname approved()
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpname cancelled()
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpname draft()
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpname inProgress()
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpname newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpname newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpname onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpname pendingApproval()
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpname query()
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpname whereApprovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpname whereApprovedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpname whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpname whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpname whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpname whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpname whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpname whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpname whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpname whereOpnameDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpname whereOpnameNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpname whereRejectReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpname whereScope($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpname whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpname whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpname whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpname whereWarehouseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpname withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpname withoutTrashed()
 */
	class StockOpname extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $stock_opname_id
 * @property int $product_id
 * @property int $system_stock
 * @property int|null $physical_stock
 * @property int $difference
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Product $product
 * @property-read \App\Models\StockOpname $stockOpname
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpnameItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpnameItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpnameItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpnameItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpnameItem whereDifference($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpnameItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpnameItem whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpnameItem wherePhysicalStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpnameItem whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpnameItem whereStockOpnameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpnameItem whereSystemStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpnameItem whereUpdatedAt($value)
 */
	class StockOpnameItem extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $warehouse_id
 * @property int $product_id
 * @property string $period_type
 * @property string $period_date
 * @property int $opening_stock
 * @property int $stock_in
 * @property int $stock_out
 * @property int $transfer_in
 * @property int $transfer_out
 * @property int $adjustment
 * @property int $closing_stock
 * @property string $total_value
 * @property string $generated_at
 * @property-read \App\Models\Product $product
 * @property-read \App\Models\Warehouse $warehouse
 * @method static \Illuminate\Database\Eloquent\Builder|StockReport daily()
 * @method static \Illuminate\Database\Eloquent\Builder|StockReport forPeriod(string $type, string $date)
 * @method static \Illuminate\Database\Eloquent\Builder|StockReport monthly()
 * @method static \Illuminate\Database\Eloquent\Builder|StockReport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StockReport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StockReport query()
 * @method static \Illuminate\Database\Eloquent\Builder|StockReport whereAdjustment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockReport whereClosingStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockReport whereGeneratedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockReport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockReport whereOpeningStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockReport wherePeriodDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockReport wherePeriodType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockReport whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockReport whereStockIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockReport whereStockOut($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockReport whereTotalValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockReport whereTransferIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockReport whereTransferOut($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockReport whereWarehouseId($value)
 */
	class StockReport extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $transfer_number
 * @property int $from_warehouse_id
 * @property int $to_warehouse_id
 * @property int $requested_by
 * @property int|null $approved_by
 * @property int|null $received_by
 * @property string $status
 * @property string $transfer_date
 * @property string|null $expected_arrival
 * @property string|null $approved_at
 * @property string|null $received_at
 * @property string|null $reject_reason
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $approvedBy
 * @property-read \App\Models\Warehouse $fromWarehouse
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StockTransferItem> $items
 * @property-read int|null $items_count
 * @property-read \App\Models\User|null $receivedBy
 * @property-read \App\Models\User $requestedBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StockMovement> $stockMovements
 * @property-read int|null $stock_movements_count
 * @property-read \App\Models\Warehouse $toWarehouse
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransfer approved()
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransfer cancelled()
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransfer inTransit()
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransfer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransfer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransfer onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransfer pending()
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransfer query()
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransfer received()
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransfer rejected()
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransfer whereApprovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransfer whereApprovedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransfer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransfer whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransfer whereExpectedArrival($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransfer whereFromWarehouseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransfer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransfer whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransfer whereReceivedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransfer whereReceivedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransfer whereRejectReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransfer whereRequestedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransfer whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransfer whereToWarehouseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransfer whereTransferDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransfer whereTransferNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransfer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransfer withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransfer withoutTrashed()
 */
	class StockTransfer extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $stock_transfer_id
 * @property int $product_id
 * @property int $quantity_requested
 * @property int $quantity_sent
 * @property int $quantity_received
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Product $product
 * @property-read \App\Models\StockTransfer $stockTransfer
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransferItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransferItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransferItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransferItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransferItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransferItem whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransferItem whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransferItem whereQuantityReceived($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransferItem whereQuantityRequested($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransferItem whereQuantitySent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransferItem whereStockTransferId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransferItem whereUpdatedAt($value)
 */
	class StockTransferItem extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string|null $contact_person
 * @property string|null $phone
 * @property string|null $email
 * @property string|null $address
 * @property string|null $city
 * @property string|null $province
 * @property string|null $npwp
 * @property string|null $bank_name
 * @property string|null $bank_account
 * @property string|null $bank_account_name
 * @property string|null $notes
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PurchaseOrder> $purchaseOrders
 * @property-read int|null $purchase_orders_count
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier query()
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier whereBankAccount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier whereBankAccountName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier whereBankName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier whereContactPerson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier whereNpwp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier whereProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier withoutTrashed()
 */
	class Supplier extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $email_verified_at
 * @property string $password
 * @property string $role
 * @property int $is_active
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ActivityLog> $activityLogs
 * @property-read int|null $activity_logs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PurchaseOrder> $approvedPurchaseOrders
 * @property-read int|null $approved_purchase_orders_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\RequestItem> $approvedRequests
 * @property-read int|null $approved_requests_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BudgetRequest> $budgetRequests
 * @property-read int|null $budget_requests_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CashBook> $cashBooks
 * @property-read int|null $cash_books_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Chat> $chatsAsOne
 * @property-read int|null $chats_as_one_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Chat> $chatsAsTwo
 * @property-read int|null $chats_as_two_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PurchaseOrder> $createdPurchaseOrders
 * @property-read int|null $created_purchase_orders_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SalesOrder> $createdSalesOrders
 * @property-read int|null $created_sales_orders_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Notification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Payment> $payments
 * @property-read int|null $payments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductSubmission> $productSubmissions
 * @property-read int|null $product_submissions_count
 * @property-read \App\Models\UserProfile|null $profile
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\RequestItem> $requests
 * @property-read int|null $requests_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ChatMessage> $sentMessages
 * @property-read int|null $sent_messages_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StockMovement> $stockMovements
 * @property-read int|null $stock_movements_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StockOpname> $stockOpnames
 * @property-read int|null $stock_opnames_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StockTransfer> $stockTransfersReceived
 * @property-read int|null $stock_transfers_received_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StockTransfer> $stockTransfersRequested
 * @property-read int|null $stock_transfers_requested_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|User active()
 * @method static \Illuminate\Database\Eloquent\Builder|User byRole(string $role)
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User withoutTrashed()
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string|null $phone
 * @property string|null $address
 * @property string|null $city
 * @property string|null $province
 * @property string|null $photo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|UserProfile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserProfile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserProfile query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserProfile whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserProfile whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserProfile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserProfile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserProfile wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserProfile wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserProfile whereProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserProfile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserProfile whereUserId($value)
 */
	class UserProfile extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string $location
 * @property string|null $address
 * @property string|null $city
 * @property string|null $province
 * @property string|null $postal_code
 * @property string|null $pic_name
 * @property string|null $pic_phone
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $photo
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BarcodeLog> $barcodeLogs
 * @property-read int|null $barcode_logs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductSubmission> $productSubmissions
 * @property-read int|null $product_submissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Product> $products
 * @property-read int|null $products_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PurchaseOrder> $purchaseOrders
 * @property-read int|null $purchase_orders_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SalesOrder> $salesOrders
 * @property-read int|null $sales_orders_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StockMovement> $stockMovements
 * @property-read int|null $stock_movements_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StockOpname> $stockOpnames
 * @property-read int|null $stock_opnames_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StockReport> $stockReports
 * @property-read int|null $stock_reports_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Stock> $stocks
 * @property-read int|null $stocks_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StockTransfer> $transfersIn
 * @property-read int|null $transfers_in_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StockTransfer> $transfersOut
 * @property-read int|null $transfers_out_count
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse query()
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse wherePicName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse wherePicPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse wherePostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse withoutTrashed()
 */
	class Warehouse extends \Eloquent {}
}

