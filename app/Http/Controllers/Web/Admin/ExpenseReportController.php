<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\BudgetRequest;
use App\Models\ExpenseReport;
use App\Services\ExpenseReportService;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ExpenseReportController extends Controller
{
    // ── GET /admin/expense-reports ──────────────────────────────
    public function index(Request $request): View
    {
        $reports = ExpenseReport::with(['budgetRequest:id,nomor_form,total_estimasi', 'submittedBy:id,name', 'verifiedBy:id,name'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->date_from, fn($q) => $q->whereDate('tanggal_transaksi', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('tanggal_transaksi', '<=', $request->date_to))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('Admin.expense_report.index', compact('reports'));
    }

    // ── GET /admin/expense-reports/create ───────────────────────
    public function create(): View
    {
        // RAB yang sudah disetujui saja yang bisa dibuatkan LPJ,
        // samain sama pengecekan di ExpenseReportService::createFromRequest().

    $budgetRequests = BudgetRequest::whereIn('status', ['approved', 'approved_revisi'])
        ->orderByDesc('created_at')
        ->get(['id', 'nomor_form', 'nama_akun', 'total_estimasi', 'total_realisasi']);

    return view('Admin.expense_report.create', compact('budgetRequests'));
}

    // ── POST /admin/expense-reports ──────────────────────────────
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
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
            'attachments.*'       => ImageService::documentRules(),
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $budgetRequest = BudgetRequest::findOrFail($request->budget_request_id);

        $attachmentPaths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $attachmentPaths[] = ImageService::upload($file, 'expenses');
            }
        }

        try {
            $er = ExpenseReportService::createFromRequest($budgetRequest, $validator->validated(), $attachmentPaths);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        $message = $er->status === 'pending_revisi'
            ? 'Nominal realisasi melebihi sisa anggaran. Laporan disimpan dan revisi anggaran otomatis diajukan, menunggu persetujuan.'
            : 'Laporan pertanggungjawaban berhasil dikirim & realisasi otomatis tercatat di buku kas.';

        return redirect()->route('admin.expense-reports.show', $er)->with('success', $message);
    }

    // ── GET /admin/expense-reports/{expenseReport} ───────────────
    public function show(ExpenseReport $expenseReport): View
    {
        $expenseReport->load(['budgetRequest', 'submittedBy:id,name', 'verifiedBy:id,name']);

        return view('Admin.expense_report.show', ['er' => $expenseReport]);
    }

    // ── GET /admin/expense-reports/{expenseReport}/edit ──────────
    public function edit(ExpenseReport $expenseReport): RedirectResponse|View
    {
        if ($expenseReport->status === 'verified') {
            return back()->with('error', 'Laporan yang sudah diverifikasi tidak dapat diubah.');
        }

        return view('Admin.expense_report.edit', ['er' => $expenseReport]);
    }

    // ── PUT /admin/expense-reports/{expenseReport} ───────────────
    public function update(Request $request, ExpenseReport $expenseReport): RedirectResponse
    {
        if ($expenseReport->status === 'verified') {
            return back()->with('error', 'Laporan yang sudah diverifikasi tidak dapat diubah.');
        }

        $validator = Validator::make($request->all(), [
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
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $expenseReport->update($request->only(
            'nomor_invoice', 'nama_vendor', 'tanggal_transaksi', 'nominal_realisasi',
            'lamp_invoice', 'lamp_bukti_transfer', 'lamp_kartu_garansi', 'lamp_serah_terima',
            'lamp_lainnya', 'catatan'
        ));

        $expenseReport->calculateSelisih();
        $expenseReport->save();

        return redirect()->route('admin.expense-reports.show', $expenseReport)->with('success', 'Laporan berhasil diupdate.');
    }

    // ── POST /admin/expense-reports/{expenseReport}/verify ───────
    public function verify(Request $request, ExpenseReport $expenseReport): RedirectResponse
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

        return redirect()->route('admin.expense-reports.show', $expenseReport)->with('success', 'Laporan berhasil diverifikasi.');
    }
}