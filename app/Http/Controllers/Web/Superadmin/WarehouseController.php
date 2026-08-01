<?php

namespace App\Http\Controllers\Web\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class WarehouseController extends Controller
{
    public function index(Request $request)
    {
        $warehouses = Warehouse::withCount('stocks')
            ->when($request->search, fn($q) => $q->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('code', 'like', "%{$request->search}%")
                    ->orWhere('location', 'like', "%{$request->search}%");
            }))
            ->when($request->has('is_active') && $request->is_active !== '', fn($q) => $q->where('is_active', $request->boolean('is_active')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('superadmin.warehouse.index', compact('warehouses'));
    }

    public function create()
    {
        return view('superadmin.warehouse.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'code'      => 'required|string|max:50|unique:warehouses,code',
            'location'  => 'required|string',
            'pic_name'  => 'nullable|string|max:255',
            'pic_phone' => 'nullable|string|max:20',
            'is_active' => 'nullable|boolean',
            'photo'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = ImageService::upload($request->file('photo'), 'warehouses');
        }

        Warehouse::create([
            'name'      => $validated['name'],
            'code'      => strtoupper($validated['code']),
            'location'  => $validated['location'],
            'pic_name'  => $validated['pic_name'] ?? null,
            'pic_phone' => $validated['pic_phone'] ?? null,
            'is_active' => $request->boolean('is_active', true),
            'photo'     => $photoPath,
        ]);

        return redirect()->route('warehouses.index')
            ->with('success', 'Gudang berhasil dibuat.');
    }

    public function show(Warehouse $warehouse)
    {
        $warehouse->loadCount('stocks')
            ->load(['stocks.product:id,name,sku,unit,min_stock,purchase_price']);

        return view('superadmin.warehouse.show', compact('warehouse'));
    }

    public function edit(Warehouse $warehouse)
    {
        return view('superadmin.warehouse.edit', compact('warehouse'));
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'code'      => ['required', 'string', 'max:50', Rule::unique('warehouses')->ignore($warehouse->id)],
            'location'  => 'required|string',
            'pic_name'  => 'nullable|string|max:255',
            'pic_phone' => 'nullable|string|max:20',
            'is_active' => 'nullable|boolean',
            'photo'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $validated;
        $data['code'] = strtoupper($validated['code']);
        $data['is_active'] = $request->boolean('is_active', $warehouse->is_active);

        if ($request->hasFile('photo')) {
            $data['photo'] = ImageService::upload($request->file('photo'), 'warehouses', $warehouse->photo);
        }

        $warehouse->update($data);

        return redirect()->route('warehouses.index')
            ->with('success', 'Gudang berhasil diupdate.');
    }

    public function destroy(Warehouse $warehouse)
    {
        $totalStock = $warehouse->stocks()->sum('quantity');
        if ($totalStock > 0) {
            return back()->with('error', "Gudang tidak dapat dihapus karena masih memiliki {$totalStock} item stok.");
        }

        if ($warehouse->photo) {
            ImageService::delete($warehouse->photo);
        }

        $warehouse->delete();

        return redirect()->route('warehouses.index')
            ->with('success', 'Gudang berhasil dihapus.');
    }

    public function toggleActive(Warehouse $warehouse)
    {
        $warehouse->update(['is_active' => !$warehouse->is_active]);

        return back()->with('success', $warehouse->is_active ? 'Gudang diaktifkan.' : 'Gudang dinonaktifkan.');
    }
}
