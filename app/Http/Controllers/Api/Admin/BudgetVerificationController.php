<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\BudgetRequest;
use App\Models\BudgetVerification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BudgetVerificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $verifications = BudgetVerification::with(['budgetRequest:id,nomor_form', 'finance:id,name'])
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

        $budgetRequest = BudgetRequest::findOrFail($request->budget_request_id);

        // Diubah: sekarang gate-nya di 'pending' (bukan 'pending_sa'), karena
        // verifikasi finance ini sekarang jadi bagian dari approve Admin,
        // bukan langkah terpisah di Super Admin.
        if ($budgetRequest->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'RAB ini sudah tidak dalam status menunggu persetujuan admin.',
            ], 422);
        }

        $bv = DB::transaction(function () use ($request, $budgetRequest) {
            $verification = BudgetVerification::create(array_merge(
                $request->only(
                    'budget_request_id',
                    'doc_form_lengkap',
                    'doc_surat_justifikasi',
                    'doc_estimasi_vendor',
                    'doc_spesifikasi_teknis',
                    'doc_lainnya',
                    'cek_anggaran',
                    'analisa_cashflow',
                    'rekomendasi',
                    'nominal_rekomendasi',
                    'catatan_finance'
                ),
                ['finance_id' => auth()->id(), 'verified_at' => now()]
        ));

            // Approve admin + hasil verifikasi disatukan di sini.
            match ($request->rekomendasi) {
                'setuju' => $budgetRequest->update([
                    'status'                 => 'pending_sa', // diteruskan ke Super Admin
                    'branch_manager_id'      => auth()->id(),
                    'branch_manager_at'      => now(),
                    'catatan_branch_manager' => $request->catatan_finance,
                ]),
                'tunda' => $budgetRequest->update([
                    'status'                 => 'ditunda',
                    'branch_manager_id'      => auth()->id(),
                    'branch_manager_at'      => now(),
                    'catatan_branch_manager' => $request->catatan_finance,
                ]),
                'tolak' => $budgetRequest->update([
                    'status'                 => 'ditolak',
                    'branch_manager_id'      => auth()->id(),
                    'branch_manager_at'      => now(),
                    'catatan_branch_manager' => $request->catatan_finance,
                    'finance_id'             => auth()->id(),
                    'finance_at'             => now(),
                ]),
            };

            return $verification;
        });

        $message = match ($request->rekomendasi) {
            'setuju' => 'RAB disetujui admin dan diteruskan ke Super Admin untuk persetujuan final.',
            'tunda'  => 'RAB ditunda. User perlu merevisi dan submit ulang.',
            'tolak'  => 'RAB ditolak.',
        };

        return response()->json(['success' => true, 'message' => $message, 'data' => $bv], 201);
    }
    public function update(Request $request, BudgetVerification $bv): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'doc_form_lengkap'         => 'nullable|boolean',
            'doc_surat_justifikasi'    => 'nullable|boolean',
            'doc_estimasi_vendor'      => 'nullable|boolean',
            'doc_spesifikasi_teknis'   => 'nullable|boolean',
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

        $bv->update($request->only(
            'doc_form_lengkap',
            'doc_surat_justifikasi',
            'doc_estimasi_vendor',
            'doc_spesifikasi_teknis',
            'doc_lainnya',
            'cek_anggaran',
            'analisa_cashflow',
            'rekomendasi',
            'nominal_rekomendasi',
            'catatan_finance'
        ));

        return response()->json(['success' => true, 'message' => 'Verifikasi berhasil diupdate.', 'data' => $bv->fresh()]);
    }
}

