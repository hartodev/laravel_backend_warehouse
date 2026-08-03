<?php

namespace App\Http\Controllers\Web\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Product;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $suppliers = Supplier::withCount('products')
            ->when($request->search, fn($q) => $q->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%")
                    ->orWhere('phone', 'like', "%{$request->search}%");
            }))
            ->when($request->has('is_active') && $request->is_active !== '', fn($q) => $q->where('is_active', $request->boolean('is_active')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('superadmin.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('superadmin.suppliers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:200|unique:suppliers,name',
            'email'   => 'nullable|email|max:100|unique:suppliers,email',
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city'    => 'nullable|string|max:100',
            'contact_person' => 'nullable|string|max:150',
            'contact_phone'  => 'nullable|string|max:20',
            'bank_account_name' => 'nullable|string|max:150',
            'bank_name'      => 'nullable|string|max:100',
            'bank_account'   => 'nullable|string|max:50',
            'bank_holder'    => 'nullable|string|max:150',
            'notes'          => 'nullable|string',
            'logo'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_active'      => 'nullable|boolean',
            'code' => 'required|string|max:50|unique:suppliers,code',
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = ImageService::upload($request->file('logo'), 'suppliers');
        }

        Supplier::create([
            'code' => $request->code,
            'name'           => $request->name,
            'email'          => $request->email,
            'phone'          => $request->phone,
            'address'        => $request->address,
            'city'           => $request->city,
            'contact_person' => $request->contact_person,
            'bank_name'      => $request->bank_name,
            'bank_account'   => $request->bank_account,
            'bank_account_name' => $request->bank_holder,
            'notes'          => $request->notes,
            'logo'           => $logoPath,
            'is_active'      => $request->boolean('is_active', true),
        ]);

        return redirect()->route('superadmin.suppliers.index')
            ->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function show(Supplier $supplier)
    {
        $supplier->loadCount('products')
            ->load(['products:id,name,sku,selling_price,is_active,supplier_id' => fn($q) => $q->limit(15)]);

        $totalPurchased = $supplier->purchaseOrders()->whereIn('status', ['approved', 'received'])->sum('total_amount');

        return view('superadmin.suppliers.show', compact('supplier', 'totalPurchased'));
    }

    public function edit(Supplier $supplier)
    {
        return view('superadmin.suppliers.edit', compact('supplier'));
    }

public function update(Request $request, Supplier $supplier)
{
    $request->validate([
        'name'              => ['required', 'string', 'max:200', Rule::unique('suppliers')->ignore($supplier->id)],
        'email'             => ['nullable', 'email', 'max:100', Rule::unique('suppliers')->ignore($supplier->id)],
        'phone'             => 'nullable|string|max:20',
        'address'           => 'nullable|string',
        'city'              => 'nullable|string|max:100',
        'contact_person'    => 'nullable|string|max:150',
        'bank_name'         => 'nullable|string|max:100',
        'bank_account'      => 'nullable|string|max:50',
        'bank_account_name' => 'nullable|string|max:150',
        'notes'             => 'nullable|string',
        'logo'              => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        'is_active'         => 'nullable|boolean',
        'code'              => ['required', 'string', 'max:50', Rule::unique('suppliers')->ignore($supplier->id)],
    ]);

    // Gunakan kolom yang benar sesuai DB — hapus contact_phone & bank_holder
    $data = $request->only([
        'name', 'email', 'phone', 'address', 'city',
        'contact_person', 'bank_name', 'bank_account', 'bank_account_name',
        'notes', 'code',
    ]);
    $data['is_active'] = $request->boolean('is_active', $supplier->is_active);

    if ($request->hasFile('logo')) {
        $data['logo'] = ImageService::upload($request->file('logo'), 'suppliers', $supplier->logo);
    }

    $supplier->update($data);

        return redirect()->route('superadmin.suppliers.index')
        ->with('success', 'Supplier berhasil diupdate.');
}
    public function destroy(Supplier $supplier)
    {
        if ($supplier->products()->count() > 0) {
            return back()->with('error', 'Supplier tidak dapat dihapus karena masih memiliki produk.');
        }

        if ($supplier->logo) {
            ImageService::delete($supplier->logo);
        }

        $supplier->delete();

        return redirect()->route('superadmin.suppliers.index')
            ->with('success', 'Supplier berhasil dihapus.');
    }

    public function toggleActive(Supplier $supplier)
    {
        $supplier->update(['is_active' => !$supplier->is_active]);

        return back()->with('success', $supplier->is_active ? 'Supplier diaktifkan.' : 'Supplier dinonaktifkan.');
    }
}

