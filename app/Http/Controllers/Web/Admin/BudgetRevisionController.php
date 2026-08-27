<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\BudgetRevision;
use App\Services\ExpenseReportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BudgetRevisionController extends Controller
{
    public function index(Request $request): View
    {
        $revisions = BudgetRevision::with(['createdBy:id,name', 'approvedBy:id,name', 'budgetRequest:id,nomor_form'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('Admin.budget_revision.index', compact('revisions'));
    }

    public function show(BudgetRevision $budgetRevision): View
    {
        $budgetRevision->load(['createdBy:id,name', 'approvedBy:id,name', 'budgetRequest', 'expenseReport']);

        return view('Admin.budget_revision.show', compact('budgetRevision'));
    }

    public function create(): View
    {
        return view('Admin.budget_revision.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
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

        $revision = new BudgetRevision($validated);
        $revision->created_by    = auth()->id();
        $revision->status        = 'pending';
        $revision->anggaran_baru = $revision->hitungAnggaranBaru();
        $revision->save();

        return redirect()->route('admin.budget-revisions.show', $revision)
            ->with('success', 'Revisi anggaran berhasil diajukan.');
    }

    public function update(Request $request, BudgetRevision $budgetRevision): RedirectResponse
    {
        if ($budgetRevision->status !== 'pending') {
            return back()->with('error', 'Revisi yang sudah diproses tidak dapat diubah.');
        }

        $validated = $request->validate([
            'akun_terdampak'    => 'sometimes|required|string|max:200',
            'kode_akun'         => 'nullable|string|max:50',
            'anggaran_awal'     => 'sometimes|required|numeric|min:0',
            'realisasi'         => 'sometimes|required|numeric|min:0',
            'jenis_perubahan'   => 'sometimes|required|in:tambahan,pengurangan',
            'nominal_perubahan' => 'sometimes|required|numeric|min:0',
            'alasan_revisi'     => 'sometimes|required|string',
        ]);

        $budgetRevision->fill($validated);
        $budgetRevision->anggaran_baru = $budgetRevision->hitungAnggaranBaru();
        $budgetRevision->save();

        return redirect()->route('admin.budget-revisions.show', $budgetRevision)
            ->with('success', 'Revisi anggaran berhasil diupdate.');
    }

    // POST /admin/budget-revisions/{budgetRevision}/approve
    // Menerapkan efek revisi ke BudgetRequest + Buku Kas
    public function approve(Request $request, BudgetRevision $budgetRevision): RedirectResponse
    {
        if ($budgetRevision->status !== 'pending') {
            return back()->with('error', 'Hanya revisi pending yang dapat disetujui.');
        }

        DB::transaction(function () use ($request, $budgetRevision) {
            $budgetRevision->applyToBudget();
            $budgetRevision->update([
                'status'           => 'approved',
                'approved_by'      => auth()->id(),
                'approved_at'      => now(),
                'catatan_approver' => $request->catatan,
            ]);

            $er = $budgetRevision->expenseReport;
            if ($er && $er->status === 'pending_revisi') {
                ExpenseReportService::finalizeRealisasi($er->fresh());
            }
        });

        return redirect()->route('admin.budget-revisions.show', $budgetRevision)
            ->with('success', 'Revisi anggaran disetujui dan diterapkan ke RAB.');
    }

    // POST /admin/budget-revisions/{budgetRevision}/reject
    public function reject(Request $request, BudgetRevision $budgetRevision): RedirectResponse
    {
        if ($budgetRevision->status !== 'pending') {
            return back()->with('error', 'Hanya revisi pending yang dapat ditolak.');
        }

        $validated = $request->validate(['catatan' => 'required|string']);

        $budgetRevision->update([
            'status'           => 'ditolak',
            'approved_by'      => auth()->id(),
            'approved_at'      => now(),
            'catatan_approver' => $validated['catatan'],
        ]);

        return redirect()->route('admin.budget-revisions.show', $budgetRevision)
            ->with('success', 'Revisi anggaran ditolak.');
    }
}