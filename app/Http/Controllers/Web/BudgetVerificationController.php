<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BudgetVerification;
use App\Models\BudgetRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Verifikasi Anggaran — Super Admin
 *
 * Gate sebelum approval final di BudgetRequestController::approve().
 * RAB berstatus 'pending_sa' diverifikasi dulu (kelengkapan dokumen,
 * cek anggaran, analisa cashflow) sebelum bisa di-approve final.
 *
 *   rekomendasi = setuju -> status RAB TETAP 'pending_sa'
 *                           (sekarang sudah punya verifikasi 'setuju',
 *                            tombol Approve di halaman RAB akan aktif)
 *   rekomendasi = tunda  -> status RAB -> 'ditunda'
 *   rekomendasi = tolak  -> status RAB -> 'ditolak'
 */
class BudgetVerificationController extends Controller
{
    public function index(Request $request)
    {
        $verifications = BudgetVerification::with(['verifiedBy:id,name', 'budgetRequest:id,nomor_form,total_estimasi'])
            ->when($request->rekomendasi, fn($q) => $q->where('rekomendasi', $request->rekomendasi))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('superadmin.budget_verification.index', compact('verifications'));
    }

    // ─────────────────────────────────────────────────────────────
    // GET /superadmin/budget-verifications/create
    // Hanya RAB 'pending_sa' yang BELUM punya verifikasi sama sekali
    // ─────────────────────────────────────────────────────────────
    public function create()
    {
        $budgetRequests = BudgetRequest::with('items:id,budget_request_id,nama_item')
            ->where('status', 'pending_sa')
            ->whereDoesntHave('budgetVerifications')
            ->get(['id', 'nomor_form', 'total_estimasi']);

        return view('superadmin.budget_verification.create', compact('budgetRequests'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'budget_request_id'      => 'required|exists:budget_requests,id',
            'doc_form_lengkap'       => 'nullable|boolean',
            'doc_surat_justifikasi'  => 'nullable|boolean',
            'doc_estimasi_vendor'    => 'nullable|boolean',
            'doc_spesifikasi_teknis' => 'nullable|boolean',
            'doc_lainnya'            => 'nullable|string',
            'cek_anggaran'           => 'nullable|string',
            'analisa_cashflow'       => 'nullable|string',
            'rekomendasi'            => 'required|in:setuju,tunda,tolak',
            'nominal_rekomendasi'    => 'nullable|numeric|min:0',
            'catatan_finance'        => 'nullable|string',
        ]);

        $budgetRequest = BudgetRequest::findOrFail($validated['budget_request_id']);

        if ($budgetRequest->status !== 'pending_sa') {
            return back()->with('error', 'RAB ini sudah tidak dalam status menunggu verifikasi.');
        }

        DB::transaction(function () use ($request, $validated, $budgetRequest) {
            BudgetVerification::create([
                'budget_request_id'      => $budgetRequest->id,
                'finance_id'             => auth()->id(),
                'doc_form_lengkap'       => $request->boolean('doc_form_lengkap'),
                'doc_surat_justifikasi'  => $request->boolean('doc_surat_justifikasi'),
                'doc_estimasi_vendor'    => $request->boolean('doc_estimasi_vendor'),
                'doc_spesifikasi_teknis' => $request->boolean('doc_spesifikasi_teknis'),
                'doc_lainnya'            => $validated['doc_lainnya'] ?? null,
                'cek_anggaran'           => $validated['cek_anggaran'] ?? null,
                'analisa_cashflow'       => $validated['analisa_cashflow'] ?? null,
                'rekomendasi'            => $validated['rekomendasi'],
                'nominal_rekomendasi'    => $validated['nominal_rekomendasi'] ?? null,
                'catatan_finance'        => $validated['catatan_finance'] ?? null,
                'verified_at'            => now(),
            ]);

            // status RAB hanya berubah untuk tunda/tolak — 'setuju' tetap
            // 'pending_sa' karena masih menunggu approval final terpisah.
            if ($validated['rekomendasi'] === 'tunda') {
                $budgetRequest->update(['status' => 'ditunda']);
            } elseif ($validated['rekomendasi'] === 'tolak') {
                $budgetRequest->update([
                    'status'                 => 'ditolak',
                    'finance_id'             => auth()->id(),
                    'finance_at'             => now(),
                    'catatan_branch_manager' => trim(($budgetRequest->catatan_branch_manager ?? '') . ' | Verifikasi: ' . ($validated['catatan_finance'] ?? 'Ditolak saat verifikasi.')),
                ]);
            }
        });

        $message = match ($validated['rekomendasi']) {
            'setuju' => 'Verifikasi selesai dengan rekomendasi setuju. RAB siap untuk di-approve final.',
            'tunda'  => 'Verifikasi selesai. RAB ditunda menunggu kelengkapan/perbaikan.',
            'tolak'  => 'Verifikasi selesai. RAB ditolak.',
        };

        return redirect()->route('superadmin.budget-verifications.index')->with('success', $message);
    }

    public function show(BudgetVerification $budgetVerification)
    {
        $budgetVerification->load(['verifiedBy:id,name', 'budgetRequest.items']);
        return view('superadmin.budget_verification.show', compact('budgetVerification'));
    }

    public function edit(BudgetVerification $budgetVerification)
    {
        $budgetRequests = BudgetRequest::with('items:id,budget_request_id,nama_item')
            ->where('status', 'pending_sa')
            ->get(['id', 'nomor_form', 'total_estimasi']);

        $budgetVerification->load('budgetRequest.items');

        return view('superadmin.budget_verification.edit', compact('budgetVerification', 'budgetRequests'));
    }

    public function update(Request $request, BudgetVerification $budgetVerification)
    {
        $validated = $request->validate([
            'doc_form_lengkap'       => 'nullable|boolean',
            'doc_surat_justifikasi'  => 'nullable|boolean',
            'doc_estimasi_vendor'    => 'nullable|boolean',
            'doc_spesifikasi_teknis' => 'nullable|boolean',
            'doc_lainnya'            => 'nullable|string',
            'cek_anggaran'           => 'nullable|string',
            'analisa_cashflow'       => 'nullable|string',
            'rekomendasi'            => 'required|in:setuju,tunda,tolak',
            'nominal_rekomendasi'    => 'nullable|numeric|min:0',
            'catatan_finance'        => 'nullable|string',
        ]);

        $budgetVerification->update([
            'doc_form_lengkap'       => $request->boolean('doc_form_lengkap'),
            'doc_surat_justifikasi'  => $request->boolean('doc_surat_justifikasi'),
            'doc_estimasi_vendor'    => $request->boolean('doc_estimasi_vendor'),
            'doc_spesifikasi_teknis' => $request->boolean('doc_spesifikasi_teknis'),
            'doc_lainnya'            => $validated['doc_lainnya'] ?? null,
            'cek_anggaran'           => $validated['cek_anggaran'] ?? null,
            'analisa_cashflow'       => $validated['analisa_cashflow'] ?? null,
            'rekomendasi'            => $validated['rekomendasi'],
            'nominal_rekomendasi'    => $validated['nominal_rekomendasi'] ?? null,
            'catatan_finance'        => $validated['catatan_finance'] ?? null,
        ]);

        return redirect()->route('superadmin.budget-verifications.show', $budgetVerification)
            ->with('success', 'Verifikasi berhasil diupdate.');
    }
}
