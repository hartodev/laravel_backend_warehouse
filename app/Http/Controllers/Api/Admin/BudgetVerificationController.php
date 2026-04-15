<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\BudgetVerification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BudgetVerificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $verifications = BudgetVerification::with(['budgetRequest:id,nomor_form,nama_item', 'finance:id,name'])
            ->when($request->rekomendasi, fn($q) => $q->where('rekomendasi', $request->rekomendasi))
            ->latest()
            ->paginate($request->per_page ?? 15);
 
        return response()->json(['success' => true, 'data' => $verifications]);
    }
 
    public function show(BudgetVerification $bv): JsonResponse
    {
        $bv->load(['budgetRequest', 'finance:id,name']);
 
        return response()->json(['success' => true, 'data' => $bv]);
    }
 
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'budget_request_id'        => 'required|exists:budget_requests,id',
            'doc_form_lengkap'         => 'required|boolean',
            'doc_surat_justifikasi'    => 'required|boolean',
            'doc_estimasi_vendor'      => 'required|boolean',
            'doc_spesifikasi_teknis'   => 'required|boolean',
            'doc_lainnya'              => 'nullable|string',
            'cek_anggaran'             => 'nullable|string',
            'analisa_cashflow'         => 'nullable|string',
            'rekomendasi'              => 'required|in:setuju,tunda,tolak',
            'nominal_rekomendasi'      => 'nullable|numeric|min:0',
            'catatan_finance'          => 'nullable|string',
        ]);
 
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }
 
        $bv = BudgetVerification::create(array_merge(
            $request->only('budget_request_id', 'doc_form_lengkap', 'doc_surat_justifikasi', 'doc_estimasi_vendor', 'doc_spesifikasi_teknis', 'doc_lainnya', 'cek_anggaran', 'analisa_cashflow', 'rekomendasi', 'nominal_rekomendasi', 'catatan_finance'),
            ['finance_id' => auth()->id()]
        ));
 
        // Update status budget request berdasarkan rekomendasi
        $br = $bv->budgetRequest;
        $br->update([
            'status'     => $request->rekomendasi === 'setuju' ? 'pending_finance' : ($request->rekomendasi === 'tolak' ? 'ditolak' : 'ditunda'),
            'finance_id' => auth()->id(),
            'finance_at' => now(),
        ]);
 
        return response()->json(['success' => true, 'message' => 'Verifikasi berhasil disimpan.', 'data' => $bv], 201);
    }
 
    public function update(Request $request, BudgetVerification $bv): JsonResponse
    {
        $bv->update($request->only('doc_form_lengkap', 'doc_surat_justifikasi', 'doc_estimasi_vendor', 'doc_spesifikasi_teknis', 'doc_lainnya', 'cek_anggaran', 'analisa_cashflow', 'rekomendasi', 'nominal_rekomendasi', 'catatan_finance'));
 
        return response()->json(['success' => true, 'message' => 'Verifikasi berhasil diupdate.', 'data' => $bv->fresh()]);
    }
}
