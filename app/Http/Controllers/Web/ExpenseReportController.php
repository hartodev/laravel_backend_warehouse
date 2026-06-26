<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ExpenseReport;
use App\Models\BudgetRequest;
use App\Services\ImageService;
use Illuminate\Http\Request;

class ExpenseReportController extends Controller
{
    public function index(Request $request)
    {
        $reports = ExpenseReport::with(['budgetRequest:id,nomor_form,nama_item', 'submittedBy:id,name', 'verifiedBy:id,name'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->date_from, fn($q) => $q->whereDate('tanggal_transaksi', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('tanggal_transaksi', '<=', $request->date_to))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('superadmin.expense_report.index', compact('reports'));
    }

    public function create()
    {
        $budgetRequests = BudgetRequest::where('status', 'approved')->get(['id', 'nomor_form', 'nama_item', 'estimasi_biaya']);
        return view('superadmin.expense_report.create', compact('budgetRequests'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'budget_request_id'   => 'required|exists:budget_requests,id',
            'nomor_invoice'       => 'nullable|string|max:150',
            'nama_vendor'         => 'nullable|string|max:200',
            'tanggal_transaksi'   => 'required|date',
            'nominal_realisasi'   => 'required|numeric|min:0',
            'lamp_invoice'        => 'nullable|boolean',
            'lamp_bukti_transfer' => 'nullable|boolean',
            'lamp_kartu_garansi'  => 'nullable|boolean',
            'lamp_serah_terima'   => 'nullable|boolean',
            'lamp_lainnya'        => 'nullable|string',
            'catatan'             => 'nullable|string',
            'attachments.*'       => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $attachmentPaths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $attachmentPaths[] = ImageService::upload($file, 'expenses');
            }
        }

        $er = ExpenseReport::create([
            'budget_request_id'   => $request->budget_request_id,
            'submitted_by'        => auth()->id(),
            'nomor_invoice'       => $request->nomor_invoice,
            'nama_vendor'         => $request->nama_vendor,
            'tanggal_transaksi'   => $request->tanggal_transaksi,
            'nominal_realisasi'   => $request->nominal_realisasi,
            'selisih'             => 0,
            'lamp_invoice'        => $request->boolean('lamp_invoice'),
            'lamp_bukti_transfer' => $request->boolean('lamp_bukti_transfer'),
            'lamp_kartu_garansi'  => $request->boolean('lamp_kartu_garansi'),
            'lamp_serah_terima'   => $request->boolean('lamp_serah_terima'),
            'lamp_lainnya'        => $request->lamp_lainnya,
            'attachment_files'    => $attachmentPaths,
            'catatan'             => $request->catatan,
            'status'              => 'submitted',
        ]);

        $er->calculateSelisih();
        $er->save();

        return redirect()->route('superadmin.expense-reports.show', $er)
            ->with('success', 'Laporan pertanggungjawaban berhasil dikirim.');
    }

    public function show(ExpenseReport $expenseReport)
    {
        $expenseReport->load(['budgetRequest', 'submittedBy:id,name', 'verifiedBy:id,name']);
        return view('superadmin.expense_report.show', compact('expenseReport'));
    }

    public function edit(ExpenseReport $expenseReport)
    {
        if ($expenseReport->status === 'verified') {
            return back()->with('error', 'Laporan yang sudah diverifikasi tidak dapat diubah.');
        }
        $budgetRequests = BudgetRequest::where('status', 'approved')->get(['id', 'nomor_form', 'nama_item']);
        return view('superadmin.expense_report.edit', compact('expenseReport', 'budgetRequests'));
    }

    public function update(Request $request, ExpenseReport $expenseReport)
    {
        if ($expenseReport->status === 'verified') {
            return back()->with('error', 'Laporan yang sudah diverifikasi tidak dapat diubah.');
        }

        $expenseReport->update($request->only('nomor_invoice', 'nama_vendor', 'tanggal_transaksi', 'nominal_realisasi', 'lamp_invoice', 'lamp_bukti_transfer', 'lamp_kartu_garansi', 'lamp_serah_terima', 'lamp_lainnya', 'catatan'));
        $expenseReport->calculateSelisih();
        $expenseReport->save();

        return redirect()->route('superadmin.expense-reports.show', $expenseReport)
            ->with('success', 'Laporan berhasil diupdate.');
    }

    public function verify(Request $request, ExpenseReport $expenseReport)
    {
        if ($expenseReport->status !== 'submitted') {
            return back()->with('error', 'Hanya laporan submitted yang dapat diverifikasi.');
        }

        $expenseReport->update([
            'status'      => 'verified',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
            'catatan'     => $request->catatan ?? $expenseReport->catatan,
        ]);

        return back()->with('success', 'Laporan berhasil diverifikasi.');
    }
}
