<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BudgetRevision;
use App\Models\BudgetRequest;
use Illuminate\Http\Request;

class BudgetRevisionController extends Controller
{
    public function index(Request $request)
    {
        $revisions = BudgetRevision::with(['createdBy:id,name', 'approvedBy:id,name', 'budgetRequest:id,nomor_form'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('superadmin.budget_revision.index', compact('revisions'));
    }

    public function create()
    {
        $budgetRequests = BudgetRequest::where('status', 'approved')->get(['id', 'nomor_form', 'nama_item']);
        return view('superadmin.budget_revision.create', compact('budgetRequests'));
    }

    public function store(Request $request)
    {
        $request->validate([
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

        $revision = new BudgetRevision($request->only('budget_request_id', 'expense_report_id', 'akun_terdampak', 'kode_akun', 'anggaran_awal', 'realisasi', 'jenis_perubahan', 'nominal_perubahan', 'alasan_revisi'));
        $revision->created_by    = auth()->id();
        $revision->status        = 'pending';
        $revision->anggaran_baru = $revision->hitungAnggaranBaru();
        $revision->save();

        return redirect()->route('superadmin.budget-revisions.index')
            ->with('success', 'Revisi anggaran berhasil diajukan.');
    }

    public function show(BudgetRevision $budgetRevision)
    {
        $budgetRevision->load(['createdBy:id,name', 'approvedBy:id,name', 'budgetRequest', 'expenseReport']);
        return view('superadmin.budget_revision.show', compact('budgetRevision'));
    }

    public function edit(BudgetRevision $budgetRevision)
    {
        if ($budgetRevision->status !== 'pending') {
            return back()->with('error', 'Revisi yang sudah diproses tidak dapat diubah.');
        }
        $budgetRequests = BudgetRequest::where('status', 'approved')->get(['id', 'nomor_form', 'nama_item']);
        return view('superadmin.budget_revision.edit', compact('budgetRevision', 'budgetRequests'));
    }

    public function update(Request $request, BudgetRevision $budgetRevision)
    {
        if ($budgetRevision->status !== 'pending') {
            return back()->with('error', 'Revisi yang sudah diproses tidak dapat diubah.');
        }

        $budgetRevision->fill($request->only('akun_terdampak', 'kode_akun', 'anggaran_awal', 'realisasi', 'jenis_perubahan', 'nominal_perubahan', 'alasan_revisi'));
        $budgetRevision->anggaran_baru = $budgetRevision->hitungAnggaranBaru();
        $budgetRevision->save();

        return redirect()->route('superadmin.budget-revisions.show', $budgetRevision)
            ->with('success', 'Revisi anggaran berhasil diupdate.');
    }

    public function approve(Request $request, BudgetRevision $budgetRevision)
    {
        if ($budgetRevision->status !== 'pending') {
            return back()->with('error', 'Hanya revisi pending yang dapat disetujui.');
        }

        $budgetRevision->update([
            'status'           => 'approved',
            'approved_by'      => auth()->id(),
            'approved_at'      => now(),
            'catatan_approver' => $request->catatan,
        ]);

        return back()->with('success', 'Revisi anggaran disetujui.');
    }

    public function reject(Request $request, BudgetRevision $budgetRevision)
    {
        if ($budgetRevision->status !== 'pending') {
            return back()->with('error', 'Hanya revisi pending yang dapat ditolak.');
        }

        $request->validate(['catatan' => 'required|string']);

        $budgetRevision->update([
            'status'           => 'ditolak',
            'approved_by'      => auth()->id(),
            'approved_at'      => now(),
            'catatan_approver' => $request->catatan,
        ]);

        return back()->with('success', 'Revisi anggaran ditolak.');
    }
}

