<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class SupplierController extends Controller
{
    // ── GET /admin/suppliers ─────────────────────────────────
    public function index(Request $request): View
    {
        $suppliers = Supplier::withCount('purchaseOrders')
            ->when($request->search, fn($q) => $q->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('code', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            }))
            ->when($request->filled('is_active'), fn($q) => $q->where('is_active', $request->boolean('is_active')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('Admin.suppliers.index', compact('suppliers'));
    }

    // ── GET /admin/suppliers/create ──────────────────────────
    public function create(): View
    {
        return view('Admin.suppliers.create');
    }

    // ── POST /admin/suppliers ────────────────────────────────
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'code'              => 'required|string|max:50|unique:suppliers,code',
            'contact_person'    => 'nullable|string|max:255',
            'phone'             => 'nullable|string|max:20',
            'email'             => 'nullable|email',
            'address'           => 'nullable|string',
            'city'              => 'nullable|string|max:100',
            'province'          => 'nullable|string|max:100',
            'npwp'              => 'nullable|string|max:30',
            'bank_name'         => 'nullable|string|max:100',
            'bank_account'      => 'nullable|string|max:50',
            'bank_account_name' => 'nullable|string|max:255',
            'notes'             => 'nullable|string',
            'is_active'         => 'nullable|boolean',
        ]);

        Supplier::create(array_merge($validated, [
            'code'      => strtoupper($validated['code']),
            'is_active' => $request->boolean('is_active', true),
        ]));

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Supplier berhasil dibuat.');
    }

    // ── GET /admin/suppliers/{supplier}/edit ─────────────────
    public function edit(Supplier $supplier): View
    {
        return view('Admin.suppliers.edit', compact('supplier'));
    }

    // ── PUT /admin/suppliers/{supplier} ──────────────────────
    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'code'              => ['required', 'string', 'max:50', Rule::unique('suppliers')->ignore($supplier->id)],
            'contact_person'    => 'nullable|string|max:255',
            'phone'             => 'nullable|string|max:20',
            'email'             => 'nullable|email',
            'address'           => 'nullable|string',
            'city'              => 'nullable|string|max:100',
            'province'          => 'nullable|string|max:100',
            'npwp'              => 'nullable|string|max:30',
            'bank_name'         => 'nullable|string|max:100',
            'bank_account'      => 'nullable|string|max:50',
            'bank_account_name' => 'nullable|string|max:255',
            'notes'             => 'nullable|string',
            'is_active'         => 'nullable|boolean',
        ]);

        $validated['code']      = strtoupper($validated['code']);
        $validated['is_active'] = $request->boolean('is_active');

        $supplier->update($validated);

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Supplier berhasil diupdate.');
    }

    // ── DELETE /admin/suppliers/{supplier} ───────────────────
    public function destroy(Supplier $supplier): RedirectResponse
    {
        if ($supplier->purchaseOrders()->exists()) {
            return redirect()->route('admin.suppliers.index')
                ->with('error', 'Supplier tidak dapat dihapus karena masih memiliki purchase order.');
        }

        $supplier->delete();

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Supplier berhasil dihapus.');
    }
}