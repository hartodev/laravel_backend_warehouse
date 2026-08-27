<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\BudgetRequest;
use App\Models\BudgetVerification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

/**
 * PENTING — baca sebelum pakai:
 * Method store() di sini ADALAH mekanisme approve/tunda/tolak RAB yang
 * sesungguhnya menurut Api/Admin (komentar di controller API-nya eksplisit:
 * "verifikasi finance ini sekarang jadi bagian dari approve Admin, bukan
 * langkah terpisah di Super Admin"). Ini mencatat checklist dokumen
 * (doc_form_lengkap, dst) SEKALIGUS mengubah status BudgetRequest.
 *
 * BudgetRequestController::approve()/reject()/tunda() yang sudah ada
 * sebelumnya HANYA mengubah status BudgetRequest tanpa mencatat checklist
 * verifikasi ini — jadi ada 2 jalur yang tumpang-tindih di project ini
 * sekarang. Aku TIDAK menghapus salah satunya karena itu keputusan produk
 * (apakah checklist dokumen wajib setiap approve, atau opsional) — tapi
 * secara teknis, method store() di sinilah yang paling sesuai Api/Admin.
 */
class BudgetVerificationController extends Controller
{
    // ── GET /admin/budget-verifications ──────────────────────────
    public function index(Request $request): View
    {
        $verifications = BudgetVerification::with(['finance:id,name', 'budgetRequest:id,nomor_form,total_estimasi'])
            ->when($request->rekomendasi, fn($q) => $q->where('rekomendasi', $request->rekomendasi))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('Admin.budget_verification.index', compact('verifications'));
    }

    // ── GET /admin/budget-verifications/create?budget_request_id=X ──
    public function create(Request $request): View
    {
        $budgetRequest = BudgetRequest::where('status', 'pending')
            ->findOrFail($request->budget_request_id);

        return view('Admin.budget_verification.create', compact('budgetRequest'));
    }

    // ── GET /admin/budget-verifications/{budgetVerification} ────
    public function show(BudgetVerification $budgetVerification): View
    {
        $budgetVerification->load(['budgetRequest', 'finance:id,name']);

        return view('Admin.budget_verification.show', compact('budgetVerification'));
    }

    // ── POST /admin/budget-verifications ─────────────────────────
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'budget_request_id'      => 'required|exists:budget_requests,id',
            'doc_form_lengkap'       => 'required|boolean',
            'doc_surat_justifikasi'  => 'required|boolean',
            'doc_estimasi_vendor'    => 'required|boolean',
            'doc_spesifikasi_teknis' => 'required|boolean',
            'doc_lainnya'            => 'nullable|string',
            'cek_anggaran'           => 'nullable|string',
            'analisa_cashflow'       => 'nullable|string',
            'rekomendasi'            => 'required|in:setuju,tunda,tolak',
            'nominal_rekomendasi'    => 'nullable|numeric|min:0',
            'catatan_finance'        => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $budgetRequest = BudgetRequest::findOrFail($request->budget_request_id);

        if ($budgetRequest->status !== 'pending') {
            return back()->with('error', 'RAB ini sudah tidak dalam status menunggu persetujuan admin.');
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

            match ($request->rekomendasi) {
                'setuju' => $budgetRequest->update([
                    'status'                 => 'pending_sa',
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

        return redirect()->route('admin.budget-verifications.show', $bv)->with('success', $message);
    }
}