<?php

namespace App\Http\Controllers\Web\Admin;

use App\Exceptions\BudgetRequestException;
use App\Http\Controllers\Controller;
use App\Models\BudgetRequest;
use App\Services\BudgetRequestAdminService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Panel web BARU untuk role 'admin' mereview RAB dari user.
 * Scope-nya setara Api\Admin\AdminBudgetRequestController (dipakai Android) —
 * BUKAN setara Web\Superadmin (yang punya wewenang verifikasi & approve final).
 *
 * File ini berdiri sendiri: tidak mengubah, tidak dipakai oleh, dan tidak
 * memakai Api\Admin\AdminBudgetRequestController maupun controller Superadmin
 * yang sudah ada.
 */
class BudgetRequestController extends Controller
{
    public function __construct(protected BudgetRequestAdminService $service)
    {
    }

    // GET /admin/budget-requests — daftar RAB (default: yang sudah disubmit, bukan draft)
    public function index(Request $request): View
    {
        $brs = BudgetRequest::with(['user:id,name', 'items', 'adminApprover:id,name', 'superAdminApprover:id,name'])
            ->when($request->jenis, fn($q) => $q->where('jenis', $request->jenis))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->divisi, fn($q) => $q->where('divisi', 'like', "%{$request->divisi}%"))
            ->when($request->urgensi, fn($q) => $q->where('urgensi', $request->urgensi))
            ->when($request->date_from, fn($q) => $q->whereDate('tanggal_pengajuan', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('tanggal_pengajuan', '<=', $request->date_to))
            ->when(! $request->status, fn($q) => $q->where('status', '!=', 'draft'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('Admin.budget_request.index', compact('brs'));
    }

    // GET /admin/budget-requests/{budgetRequest}
    public function show(BudgetRequest $budgetRequest): View
    {
        $budgetRequest->load([
            'user:id,name,email',
            'items',
            'adminApprover:id,name',
            'superAdminApprover:id,name',
        ]);

        return view('Admin.budget_request.show', compact('budgetRequest'));
    }

    // POST /admin/budget-requests/{budgetRequest}/approve
    public function approve(Request $request, BudgetRequest $budgetRequest): RedirectResponse
    {
        try {
            $this->service->approve($budgetRequest, auth()->user(), $request->catatan);
        } catch (BudgetRequestException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Pengajuan RAB disetujui admin. Diteruskan ke Super Admin untuk persetujuan final.');
    }

    // POST /admin/budget-requests/{budgetRequest}/reject
    public function reject(Request $request, BudgetRequest $budgetRequest): RedirectResponse
    {
        $request->validate(['catatan' => 'required|string|max:500']);

        try {
            $this->service->reject($budgetRequest, auth()->user(), $request->catatan);
        } catch (BudgetRequestException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Pengajuan RAB ditolak.');
    }

    // POST /admin/budget-requests/{budgetRequest}/tunda
    public function tunda(Request $request, BudgetRequest $budgetRequest): RedirectResponse
    {
        $request->validate(['catatan' => 'required|string|max:500']);

        try {
            $this->service->tunda($budgetRequest, auth()->user(), $request->catatan);
        } catch (BudgetRequestException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Pengajuan RAB ditunda. User dapat merevisi dan submit ulang.');
    }
}
