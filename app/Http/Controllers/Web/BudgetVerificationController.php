<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BudgetVerification;
use App\Models\BudgetRequest;
use Illuminate\Http\Request;

class BudgetVerificationController extends Controller
{
    public function index(Request $request)
    {
        $verifications = BudgetVerification::with(['verifiedBy:id,name', 'budgetRequest:id,nomor_form,nama_item'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('superadmin.budget_verification.index', compact('verifications'));
    }

    public function create()
    {
        $budgetRequests = BudgetRequest::where('status', 'approved')->get(['id', 'nomor_form', 'nama_item', 'estimasi_biaya']);
        return view('superadmin.budget_verification.create', compact('budgetRequests'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'budget_request_id' => 'required|exists:budget_requests,id',
            'jumlah_disetujui'  => 'required|numeric|min:0',
            'catatan'           => 'nullable|string',
            'status'            => 'required|in:approved,partial,rejected',
        ]);

        BudgetVerification::create([
            'budget_request_id' => $request->budget_request_id,
            'verified_by'       => auth()->id(),
            'jumlah_disetujui'  => $request->jumlah_disetujui,
            'catatan'           => $request->catatan,
            'status'            => $request->status,
            'verified_at'       => now(),
        ]);

        return redirect()->route('superadmin.budget-verifications.index')
            ->with('success', 'Verifikasi anggaran berhasil disimpan.');
    }

    public function show(BudgetVerification $budgetVerification)
    {
        $budgetVerification->load(['verifiedBy:id,name', 'budgetRequest']);
        return view('superadmin.budget_verification.show', compact('budgetVerification'));
    }

    public function edit(BudgetVerification $budgetVerification)
    {
        $budgetRequests = BudgetRequest::where('status', 'approved')->get(['id', 'nomor_form', 'nama_item']);
        return view('superadmin.budget_verification.edit', compact('budgetVerification', 'budgetRequests'));
    }

    public function update(Request $request, BudgetVerification $budgetVerification)
    {
        $request->validate([
            'jumlah_disetujui' => 'required|numeric|min:0',
            'catatan'          => 'nullable|string',
            'status'           => 'required|in:approved,partial,rejected',
        ]);

        $budgetVerification->update($request->only('jumlah_disetujui', 'catatan', 'status'));

        return redirect()->route('superadmin.budget-verifications.show', $budgetVerification)
            ->with('success', 'Verifikasi berhasil diupdate.');
    }
}

