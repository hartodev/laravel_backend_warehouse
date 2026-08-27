<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\BudgetRequest;
use Illuminate\Http\Request;

/**
 * Review RAB dari user oleh Admin.
 * Flow:
 *   pending    → admin approve → pending_sa (tunggu Super Admin)
 *   pending    → admin reject  → ditolak
 *   pending    → admin tunda   → ditunda
 *   pending_sa → ditangani Superadmin, bukan di sini.
 */
class BudgetRequestController extends Controller
{
    public function index(Request $request)
    {
        $brs = BudgetRequest::with(['user:id,name', 'adminApprover:id,name', 'superAdminApprover:id,name'])
            ->withCount('items')
            ->when($request->jenis, fn($q) => $q->where('jenis', $request->jenis))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->urgensi, fn($q) => $q->where('urgensi', $request->urgensi))
            ->when($request->divisi, fn($q) => $q->where('divisi', 'like', "%{$request->divisi}%"))
            ->when($request->date_from, fn($q) => $q->whereDate('tanggal_pengajuan', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('tanggal_pengajuan', '<=', $request->date_to))
            ->when(! $request->status, fn($q) => $q->where('status', '!=', 'draft'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('Admin.budget_request.index', compact('brs'));
    }

    public function show(BudgetRequest $budgetRequest)
    {
        $budgetRequest->load([
            'user:id,name,email',
            'items',
            'adminApprover:id,name',
            'superAdminApprover:id,name',
        ]);

        return view('Admin.budget_request.show', compact('budgetRequest'));
    }

    public function approve(Request $request, BudgetRequest $budgetRequest)
    {
        if ($budgetRequest->status !== 'pending') {
            return back()->with('error', 'Hanya pengajuan dengan status "pending" yang dapat disetujui admin.');
        }

        $budgetRequest->update([
            'status'                 => 'pending_sa',
            'branch_manager_id'      => auth()->id(),
            'branch_manager_at'      => now(),
            'catatan_branch_manager' => $request->catatan,
        ]);

        return back()->with('success', 'Pengajuan RAB disetujui. Diteruskan ke Super Admin untuk persetujuan final.');
    }

    public function reject(Request $request, BudgetRequest $budgetRequest)
    {
        if ($budgetRequest->status !== 'pending') {
            return back()->with('error', 'Hanya pengajuan dengan status "pending" yang dapat ditolak admin.');
        }

        $request->validate(['catatan' => 'required|string|max:500']);

        $budgetRequest->update([
            'status'                 => 'ditolak',
            'branch_manager_id'      => auth()->id(),
            'branch_manager_at'      => now(),
            'catatan_branch_manager' => $request->catatan,
        ]);

        return back()->with('success', 'Pengajuan RAB ditolak.');
    }

    public function tunda(Request $request, BudgetRequest $budgetRequest)
    {
        if ($budgetRequest->status !== 'pending') {
            return back()->with('error', 'Hanya pengajuan dengan status "pending" yang dapat ditunda.');
        }

        $request->validate(['catatan' => 'required|string|max:500']);

        $budgetRequest->update([
            'status'                 => 'ditunda',
            'branch_manager_id'      => auth()->id(),
            'branch_manager_at'      => now(),
            'catatan_branch_manager' => $request->catatan,
        ]);

        return back()->with('success', 'Pengajuan RAB ditunda. User dapat merevisi dan submit ulang.');
    }
}