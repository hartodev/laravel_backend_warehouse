<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\BudgetRequest;
use App\Models\BudgetRevision;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Revisi Anggaran — sisi User
 *
 * Flow:
 *   User ajukan revisi atas RAB miliknya yang sudah 'approved'
 *   -> jenis_perubahan = pengurangan          -> langsung diterapkan otomatis
 *   -> jenis_perubahan = tambahan & dana cukup -> langsung diterapkan otomatis
 *   -> jenis_perubahan = tambahan & dana kurang -> status 'pending',
 *      menunggu review manual oleh Admin/Super Admin
 *      (lihat App\Http\Controllers\Web\BudgetRevisionController::approve)
 */
class BudgetRevisionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $revisions = BudgetRevision::with(['budgetRequest:id,nomor_form,divisi', 'approvedBy:id,name'])
            ->whereHas('budgetRequest', fn($q) => $q->where('user_id', auth()->id()))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate($request->per_page ?? 15);

        return response()->json(['success' => true, 'data' => $revisions]);
    }

    public function show(BudgetRevision $budgetRevision): JsonResponse
    {
        if ($budgetRevision->budgetRequest->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses ke revisi ini.'], 403);
        }

        $budgetRevision->load(['budgetRequest', 'approvedBy:id,name']);

        return response()->json(['success' => true, 'data' => $budgetRevision]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'budget_request_id' => 'required|exists:budget_requests,id',
            'jenis_perubahan'   => 'required|in:tambahan,pengurangan',
            'nominal_perubahan' => 'required|numeric|min:1',
            'alasan_revisi'     => 'required|string',
            'kode_akun'         => 'nullable|string|max:50',
        ]);

        $budgetRequest = BudgetRequest::findOrFail($validated['budget_request_id']);

        if ($budgetRequest->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Anda hanya dapat mengajukan revisi untuk RAB milik Anda sendiri.'], 403);
        }

        if (! in_array($budgetRequest->status, ['approved', 'approved_revisi'])) {
            return response()->json(['success' => false, 'message' => 'Hanya RAB yang sudah disetujui yang dapat direvisi.'], 422);
        }

        if ($validated['jenis_perubahan'] === 'pengurangan' && $validated['nominal_perubahan'] > $budgetRequest->total_estimasi) {
            return response()->json(['success' => false, 'message' => 'Nominal pengurangan tidak boleh melebihi total anggaran saat ini.'], 422);
        }

        $revision = new BudgetRevision([
            'budget_request_id' => $budgetRequest->id,
            'akun_terdampak'    => $budgetRequest->divisi,
            'kode_akun'         => $validated['kode_akun'] ?? $budgetRequest->kode_akun,
            'anggaran_awal'     => $budgetRequest->total_estimasi,
            'realisasi'         => $budgetRequest->total_realisasi,
            'jenis_perubahan'   => $validated['jenis_perubahan'],
            'nominal_perubahan' => $validated['nominal_perubahan'],
            'alasan_revisi'     => $validated['alasan_revisi'],
        ]);
        $revision->created_by    = auth()->id();
        $revision->status        = 'pending';
        $revision->anggaran_baru = $revision->hitungAnggaranBaru();
        $revision->save();

        $diterapkanOtomatis = $revision->evaluateAndApply();

        $message = $diterapkanOtomatis
            ? 'Revisi anggaran berhasil diajukan dan langsung diterapkan (dana mencukupi).'
            : 'Revisi anggaran berhasil diajukan. Dana belum mencukupi, menunggu review Admin/Super Admin.';

        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $revision->fresh(['budgetRequest']),
        ], 201);
    }
}



