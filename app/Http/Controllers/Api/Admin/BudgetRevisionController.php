<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\BudgetRevision;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BudgetRevisionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $revisions = BudgetRevision::with(['createdBy:id,name', 'approvedBy:id,name', 'budgetRequest:id,nomor_form'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate($request->per_page ?? 15);
 
        return response()->json(['success' => true, 'data' => $revisions]);
    }
 
    public function show(BudgetRevision $br): JsonResponse
    {
        $br->load(['createdBy:id,name', 'approvedBy:id,name', 'budgetRequest', 'expenseReport']);
 
        return response()->json(['success' => true, 'data' => $br]);
    }
 
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'budget_request_id' => 'nullable|exists:budget_requests,id',
            'expense_report_id' => 'nullable|exists:expense_reports,id',
            'akun_terdampak'    => 'required|string|max:200',
            'kode_akun'         => 'nullable|string|max:50',
            'anggaran_awal'     => 'required|numeric|min:0',
            'realisasi'         => 'required|numeric|min:0',
            'jenis_perubahan'   => 'required|in:tambahan,pengurangan',
            'nominal_perubahan' => 'required|numeric|min:0',
            'alasan_revisi'     => 'required|string',
        ]);
 
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }
 
        $revision = new BudgetRevision($request->only('budget_request_id', 'expense_report_id', 'akun_terdampak', 'kode_akun', 'anggaran_awal', 'realisasi', 'jenis_perubahan', 'nominal_perubahan', 'alasan_revisi'));
        $revision->created_by   = auth()->id();
        $revision->status       = 'pending';
        $revision->anggaran_baru = $revision->hitungAnggaranBaru();
        $revision->save();
 
        return response()->json(['success' => true, 'message' => 'Revisi anggaran berhasil diajukan.', 'data' => $revision], 201);
    }
 
    public function update(Request $request, BudgetRevision $br): JsonResponse
    {
        if ($br->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Revisi yang sudah diproses tidak dapat diubah.'], 422);
        }
 
        $br->fill($request->only('akun_terdampak', 'kode_akun', 'anggaran_awal', 'realisasi', 'jenis_perubahan', 'nominal_perubahan', 'alasan_revisi'));
        $br->anggaran_baru = $br->hitungAnggaranBaru();
        $br->save();
 
        return response()->json(['success' => true, 'message' => 'Revisi anggaran berhasil diupdate.', 'data' => $br->fresh()]);
    }
 
    // POST approve
    public function approve(Request $request, BudgetRevision $br): JsonResponse
    {
        if ($br->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Hanya revisi pending yang dapat disetujui.'], 422);
        }
 
        $br->update([
            'status'            => 'approved',
            'approved_by'       => auth()->id(),
            'approved_at'       => now(),
            'catatan_approver'  => $request->catatan,
        ]);
 
        return response()->json(['success' => true, 'message' => 'Revisi anggaran disetujui.', 'data' => $br->fresh()]);
    }
 
    // POST reject
    public function reject(Request $request, BudgetRevision $br): JsonResponse
    {
        if ($br->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Hanya revisi pending yang dapat ditolak.'], 422);
        }
 
        $validator = Validator::make($request->all(), ['catatan' => 'required|string']);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Catatan penolakan wajib diisi.', 'errors' => $validator->errors()], 422);
        }
 
        $br->update([
            'status'           => 'ditolak',
            'approved_by'      => auth()->id(),
            'approved_at'      => now(),
            'catatan_approver' => $request->catatan,
        ]);
 
        return response()->json(['success' => true, 'message' => 'Revisi anggaran ditolak.', 'data' => $br->fresh()]);
    }
}
