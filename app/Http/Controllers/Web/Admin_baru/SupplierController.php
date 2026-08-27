<?php

namespace App\Http\Controllers\Web\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    private array $fields = [
        'name', 'contact_person', 'phone', 'email', 'address',
        'city', 'province', 'npwp', 'bank_name', 'bank_account',
        'bank_account_name', 'notes',
    ];

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

        return view('superadmin.suppliers.index', compact('suppliers'));
    }

    public function create(): View
    {
        return view('superadmin.suppliers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
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

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Supplier::create(array_merge(
            $request->only($this->fields),
            [
                'code'      => strtoupper($request->code),
                'is_active' => $request->boolean('is_active', true),
            ]
        ));

        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil dibuat.');
    }

    public function edit(Supplier $supplier): View
    {
        return view('superadmin.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
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

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $supplier->update(array_merge(
            $request->only($this->fields),
            [
                'code'      => strtoupper($request->code),
                'is_active' => $request->boolean('is_active'),
            ]
        ));

        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil diupdate.');
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        if ($supplier->purchaseOrders()->exists()) {
            return redirect()->back()->with('error', 'Supplier tidak dapat dihapus karena masih memiliki purchase order.');
        }

        $supplier->delete();

        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil dihapus.');
    }
}
