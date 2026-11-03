<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\BudgetRequest;
use App\Services\CashBookService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Controller untuk ADMIN mereview RAB dari user
 * Flow yang benar:
 *   pending        → admin approve  → pending_sa (tunggu super admin)
 *   pending        → admin reject   → ditolak
 *   pending        → admin tunda    → ditunda
 *   pending_sa     → (super admin handle, bukan admin)
 */
class AdminBudgetRequestController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    // GET /api/admin/budget-requests
    // Admin lihat semua RAB yang masuk (status: pending ke atas)
    // ─────────────────────────────────────────────────────────────
    public function index(Request $request): JsonResponse
    {
        $brs = BudgetRequest::with(['user:id,name', 'items', 'adminApprover:id,name', 'superAdminApprover:id,name'])
            ->when($request->jenis, fn($q) => $q->where('jenis', $request->jenis))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->divisi, fn($q) => $q->where('divisi', 'like', "%{$request->divisi}%"))
            ->when($request->urgensi, fn($q) => $q->where('urgensi', $request->urgensi))
            ->when($request->date_from, fn($q) => $q->whereDate('tanggal_pengajuan', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('tanggal_pengajuan', '<=', $request->date_to))
            // Default: tampilkan yang sudah disubmit (bukan draft)
            ->when(! $request->status, fn($q) => $q->where('status', '!=', 'draft'))
            ->latest()
            ->paginate($request->per_page ?? 15);

        return response()->json(['success' => true, 'data' => $brs]);
    }

    // ─────────────────────────────────────────────────────────────
    // GET /api/admin/budget-requests/{br}
    // ─────────────────────────────────────────────────────────────
    public function show(BudgetRequest $br): JsonResponse
    {
        $br->load([
            'user:id,name,email',
            'items',
            'adminApprover:id,name',
            'superAdminApprover:id,name',
        ]);

        return response()->json(['success' => true, 'data' => $br]);
    }

    // ─────────────────────────────────────────────────────────────
    // POST /api/admin/budget-requests/{br}/approve
    // Admin approve → forward ke super admin (status: pending_sa)
    // ─────────────────────────────────────────────────────────────
    public function approve(Request $request, BudgetRequest $br): JsonResponse
    {
        if ($br->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya pengajuan dengan status "pending" yang dapat disetujui admin.',
            ], 422);
        }

        $br->update([
            'status'              => 'pending_sa',   // tunggu super admin
            'branch_manager_id'   => auth()->id(),
            'branch_manager_at'   => now(),
            'catatan_branch_manager' => $request->catatan,
        ]);

        NotificationService::budgetRequest($br->fresh(), 'forwarded', auth()->id());

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan RAB disetujui admin. Diteruskan ke Super Admin untuk persetujuan final.',
            'data'    => $br->fresh()->load(['user:id,name', 'items', 'adminApprover:id,name']),
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // POST /api/admin/budget-requests/{br}/reject
    // Admin tolak langsung
    // ─────────────────────────────────────────────────────────────
    public function reject(Request $request, BudgetRequest $br): JsonResponse
    {
        if ($br->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya pengajuan dengan status "pending" yang dapat ditolak admin.',
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'catatan' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Alasan penolakan wajib diisi.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $br->update([
            'status'                 => 'ditolak',
            'branch_manager_id'      => auth()->id(),
            'branch_manager_at'      => now(),
            'catatan_branch_manager' => $request->catatan,
        ]);

        NotificationService::budgetRequest($br->fresh(), 'rejected', auth()->id());

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan RAB ditolak.',
            'data'    => $br->fresh()->load(['user:id,name', 'items']),
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // POST /api/admin/budget-requests/{br}/tunda
    // Admin tunda (butuh revisi dari user)
    // ─────────────────────────────────────────────────────────────
    public function tunda(Request $request, BudgetRequest $br): JsonResponse
    {
        if ($br->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya pengajuan dengan status "pending" yang dapat ditunda.',
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'catatan' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Catatan penundaan wajib diisi.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $br->update([
            'status'                 => 'ditunda',
            'branch_manager_id'      => auth()->id(),
            'branch_manager_at'      => now(),
            'catatan_branch_manager' => $request->catatan,
        ]);

        NotificationService::budgetRequest($br->fresh(), 'postponed', auth()->id());

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan RAB ditunda. User dapat merevisi dan submit ulang.',
            'data'    => $br->fresh()->load(['user:id,name', 'items']),
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // POST /api/admin/budget-requests/{br}/approve-final   (super_admin)
    // Sama dengan Web\Superadmin\BudgetRequestController@approve
    // ─────────────────────────────────────────────────────────────
    public function approveFinal(Request $request, BudgetRequest $br): JsonResponse
    {
        if ($br->status !== 'pending_sa') {
            return response()->json(['success' => false, 'message' => 'Hanya pengajuan berstatus "Menunggu Super Admin" yang dapat disetujui final.'], 422);
        }

        if (! $br->budgetVerifications()->where('rekomendasi', 'setuju')->exists()) {
            return response()->json(['success' => false, 'message' => 'RAB ini belum diverifikasi dengan rekomendasi "setuju". Selesaikan verifikasi anggaran terlebih dahulu.'], 422);
        }

        $validator = Validator::make($request->all(), ['catatan' => 'nullable|string|max:500']);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }

        DB::transaction(function () use ($request, $br) {
            $br->update(['status' => 'approved', 'finance_id' => auth()->id(), 'finance_at' => now()]);

            CashBookService::record([
                'tanggal'           => now()->toDateString(),
                'keterangan'        => "Alokasi RAB #{$br->nomor_form} — {$br->divisi}",
                'jenis'             => 'alokasi_rab',
                'jumlah_uang'       => $br->total_estimasi,
                'pihak'             => $br->divisi,
                'tipe'              => 'masuk',
                'budget_request_id' => $br->id,
                'created_by'        => auth()->id(),
                'catatan'           => $request->catatan,
            ]);
        });

        NotificationService::budgetRequest($br->fresh(), 'approved_final', auth()->id());

        return response()->json(['success' => true, 'message' => 'RAB disetujui final dan dana dialokasikan.', 'data' => $br->fresh()->load(['user:id,name', 'items'])]);
    }

    // POST /api/admin/budget-requests/{br}/reject-final   (super_admin)
    public function rejectFinal(Request $request, BudgetRequest $br): JsonResponse
    {
        if ($br->status !== 'pending_sa') {
            return response()->json(['success' => false, 'message' => 'Hanya pengajuan berstatus "Menunggu Super Admin" yang dapat ditolak di sini.'], 422);
        }

        // Terima reject_reason (web) atau catatan (konvensi API admin)
        $reason = $request->reject_reason ?? $request->catatan;
        if (! $reason || mb_strlen($reason) > 500) {
            return response()->json(['success' => false, 'message' => 'Alasan penolakan wajib diisi (maks. 500 karakter).'], 422);
        }

        $br->update([
            'status'                 => 'ditolak',
            'finance_id'             => auth()->id(),
            'finance_at'             => now(),
            'catatan_branch_manager' => trim(($br->catatan_branch_manager ?? '') . ' | SA: ' . $reason),
        ]);

        NotificationService::budgetRequest($br->fresh(), 'rejected_final', auth()->id());

        return response()->json(['success' => true, 'message' => 'RAB ditolak pada tahap final.', 'data' => $br->fresh()->load(['user:id,name', 'items'])]);
    }

    // POST /api/admin/budget-requests/{br}/realisasi   (super_admin)
    public function realisasi(Request $request, BudgetRequest $br): JsonResponse
    {
        if ($br->status !== 'approved') {
            return response()->json(['success' => false, 'message' => 'Hanya RAB yang sudah disetujui yang dapat dicatat realisasinya.'], 422);
        }

        $validator = Validator::make($request->all(), [
            'jumlah_uang' => 'required|numeric|min:1',
            'keterangan'  => 'required|string|max:255',
            'tanggal'     => 'required|date',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }

        $sisa = $br->total_estimasi - ($br->total_realisasi ?? 0);
        if ($request->jumlah_uang > $sisa) {
            return response()->json(['success' => false, 'message' => 'Jumlah realisasi melebihi sisa anggaran (Rp ' . number_format($sisa, 0, ',', '.') . ').'], 422);
        }

        DB::transaction(function () use ($request, $br) {
            $br->increment('total_realisasi', $request->jumlah_uang);

            CashBookService::record([
                'tanggal'           => $request->tanggal,
                'keterangan'        => "Realisasi RAB #{$br->nomor_form} — {$request->keterangan}",
                'jenis'             => 'realisasi_rab',
                'jumlah_uang'       => $request->jumlah_uang,
                'pihak'             => $br->divisi,
                'tipe'              => 'keluar',
                'budget_request_id' => $br->id,
                'created_by'        => auth()->id(),
            ]);
        });

        NotificationService::budgetRequest($br->fresh(), 'realisasi', auth()->id());

        return response()->json(['success' => true, 'message' => 'Realisasi dana dicatat.', 'data' => $br->fresh()]);
    }
}
