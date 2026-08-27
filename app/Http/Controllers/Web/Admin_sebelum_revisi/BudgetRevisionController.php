<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\BudgetRevision;
use Illuminate\Http\Request;

class BudgetRevisionController extends Controller
{
    /**
     * Read-only untuk Admin — approve/reject revisi tetap wewenang
     * Superadmin.
     */
    public function index(Request $request)
    {
        $revisions = BudgetRevision::with(['createdBy:id,name', 'approvedBy:id,name', 'budgetRequest:id,nomor_form'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('Admin.budget_revision.index', compact('revisions'));
    }
}