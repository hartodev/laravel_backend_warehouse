<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\BudgetRequest;
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

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan RAB ditunda. User dapat merevisi dan submit ulang.',
            'data'    => $br->fresh()->load(['user:id,name', 'items']),
        ]);
    }
}








