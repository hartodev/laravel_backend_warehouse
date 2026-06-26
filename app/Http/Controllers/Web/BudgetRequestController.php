<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BudgetRequest;
use Illuminate\Http\Request;

class BudgetRequestController extends Controller
{
    public function index(Request $request)
    {
        $brs = BudgetRequest::with(['user:id,name', 'branchManager:id,name', 'finance:id,name'])
            ->when($request->jenis, fn($q) => $q->where('jenis', $request->jenis))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->divisi, fn($q) => $q->where('divisi', 'like', "%{$request->divisi}%"))
            ->when($request->date_from, fn($q) => $q->whereDate('tanggal_pengajuan', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('tanggal_pengajuan', '<=', $request->date_to))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('superadmin.budget_request.index', compact('brs'));
    }

    public function create()
    {
        return view('superadmin.budget_request.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'divisi'            => 'required|string|max:100',
            'tanggal_pengajuan' => 'required|date',
            'jenis'             => 'required|in:rab,luar_rab',
            'kode_akun'         => 'nullable|string|max:50',
            'nama_akun'         => 'nullable|string|max:100',
            'nama_item'         => 'required|string|max:255',
            'qty'               => 'nullable|numeric|min:0',
            'satuan'            => 'nullable|string|max:50',
            'estimasi_biaya'    => 'required|numeric|min:0',
            'keterangan'        => 'nullable|string',
            'alasan_luar_rab'   => 'required_if:jenis,luar_rab|nullable|string',
            'urgensi'           => 'nullable|in:normal,mendesak',
            'dampak_jika_tidak' => 'nullable|string',
            'sumber_dana'       => 'nullable|in:realokasi,tambahan,lainnya',
        ]);

        $nomorForm = BudgetRequest::generateNomorForm($request->jenis, $request->divisi);

        BudgetRequest::create([
            'nomor_form'        => $nomorForm,
            'user_id'           => auth()->id(),
            'divisi'            => $request->divisi,
            'tanggal_pengajuan' => $request->tanggal_pengajuan,
            'jenis'             => $request->jenis,
            'kode_akun'         => $request->kode_akun,
            'nama_akun'         => $request->nama_akun,
            'nama_item'         => $request->nama_item,
            'qty'               => $request->qty,
            'satuan'            => $request->satuan,
            'estimasi_biaya'    => $request->estimasi_biaya,
            'total'             => $request->estimasi_biaya,
            'keterangan'        => $request->keterangan,
            'alasan_luar_rab'   => $request->alasan_luar_rab,
            'urgensi'           => $request->urgensi ?? 'normal',
            'dampak_jika_tidak' => $request->dampak_jika_tidak,
            'sumber_dana'       => $request->sumber_dana,
            'status'            => 'draft',
        ]);

        return redirect()->route('superadmin.budget-requests.index')
            ->with('success', 'Pengajuan anggaran berhasil dibuat.');
    }

    public function show(BudgetRequest $budgetRequest)
    {
        $budgetRequest->load(['user:id,name', 'branchManager:id,name', 'finance:id,name', 'verification', 'expenseReport', 'revision']);
        return view('superadmin.budget_request.show', compact('budgetRequest'));
    }

    public function edit(BudgetRequest $budgetRequest)
    {
        if (!in_array($budgetRequest->status, ['draft', 'pending'])) {
            return back()->with('error', 'Pengajuan yang sudah diproses tidak dapat diubah.');
        }
        return view('superadmin.budget_request.edit', compact('budgetRequest'));
    }

    public function update(Request $request, BudgetRequest $budgetRequest)
    {
        if (!in_array($budgetRequest->status, ['draft', 'pending'])) {
            return back()->with('error', 'Pengajuan yang sudah diproses tidak dapat diubah.');
        }

        $budgetRequest->update($request->only('divisi', 'nama_item', 'qty', 'satuan', 'estimasi_biaya', 'keterangan', 'alasan_luar_rab', 'urgensi', 'dampak_jika_tidak', 'sumber_dana'));

        return redirect()->route('superadmin.budget-requests.show', $budgetRequest)
            ->with('success', 'Pengajuan berhasil diupdate.');
    }

    public function destroy(BudgetRequest $budgetRequest)
    {
        if ($budgetRequest->status !== 'draft') {
            return back()->with('error', 'Hanya pengajuan draft yang dapat dihapus.');
        }

        $budgetRequest->delete();

        return redirect()->route('superadmin.budget-requests.index')
            ->with('success', 'Pengajuan berhasil dihapus.');
    }

    public function submit(BudgetRequest $budgetRequest)
    {
        if ($budgetRequest->status !== 'draft') {
            return back()->with('error', 'Hanya pengajuan draft yang dapat disubmit.');
        }

        $budgetRequest->update(['status' => 'pending']);

        return back()->with('success', 'Pengajuan berhasil dikirim untuk persetujuan.');
    }

    public function approve(Request $request, BudgetRequest $budgetRequest)
    {
        if (!in_array($budgetRequest->status, ['pending', 'pending_finance'])) {
            return back()->with('error', 'Status pengajuan tidak sesuai untuk disetujui.');
        }

        $budgetRequest->update([
            'status'                 => 'approved',
            'branch_manager_id'      => auth()->id(),
            'branch_manager_at'      => now(),
            'catatan_branch_manager' => $request->catatan,
        ]);

        return back()->with('success', 'Pengajuan anggaran disetujui.');
    }

    public function reject(Request $request, BudgetRequest $budgetRequest)
    {
        $request->validate(['reject_reason' => 'required|string']);

        $budgetRequest->update([
            'status'                 => 'ditolak',
            'branch_manager_id'      => auth()->id(),
            'branch_manager_at'      => now(),
            'catatan_branch_manager' => $request->reject_reason,
        ]);

        return back()->with('success', 'Pengajuan anggaran ditolak.');
    }
}
