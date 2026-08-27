<?php

namespace App\Http\Controllers\Web\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class WarehouseController extends Controller
{
    // ── GET /warehouses ──────────────────────────────────────
    public function index(Request $request): View
    {
        $warehouses = Warehouse::withCount('stocks')
            ->when($request->search, fn($q) => $q->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('code', 'like', "%{$request->search}%")
                  ->orWhere('location', 'like', "%{$request->search}%");
            }))
            ->when($request->filled('is_active'), fn($q) => $q->where('is_active', $request->boolean('is_active')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('superadmin.warehouses.index', compact('warehouses'));
    }

    // ── GET /warehouses/create ───────────────────────────────
    public function create(): View
    {
        return view('superadmin.warehouses.create');
    }

    // ── POST /warehouses ─────────────────────────────────────
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|max:255',
            'code'      => 'required|string|max:50|unique:warehouses,code',
            'location'  => 'required|string',
            'pic_name'  => 'nullable|string|max:255',
            'pic_phone' => 'nullable|string|max:20',
            'is_active' => 'nullable|boolean',
            'photo'     => ImageService::rules(),
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = ImageService::upload($request->file('photo'), 'warehouses');
        }

        Warehouse::create([
            'name'      => $request->name,
            'code'      => strtoupper($request->code),
            'location'  => $request->location,
            'pic_name'  => $request->pic_name,
            'pic_phone' => $request->pic_phone,
            'is_active' => $request->boolean('is_active', true),
            'photo'     => $photoPath,
        ]);

        return redirect()->route('warehouses.index')->with('success', 'Gudang berhasil dibuat.');
    }

    // ── GET /warehouses/{warehouse} ──────────────────────────
    public function show(Warehouse $warehouse): View
    {
        $warehouse->loadCount('stocks')
                  ->load(['stocks.product:id,name,sku,unit']);

        return view('superadmin.warehouses.show', compact('warehouse'));
    }

    // ── GET /warehouses/{warehouse}/edit ─────────────────────
    public function edit(Warehouse $warehouse): View
    {
        return view('superadmin.warehouses.edit', compact('warehouse'));
    }

    // ── PUT /warehouses/{warehouse} ──────────────────────────
    public function update(Request $request, Warehouse $warehouse): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|max:255',
            'code'      => ['required', 'string', 'max:50', Rule::unique('warehouses')->ignore($warehouse->id)],
            'location'  => 'required|string',
            'pic_name'  => 'nullable|string|max:255',
            'pic_phone' => 'nullable|string|max:20',
            'is_active' => 'nullable|boolean',
            'photo'     => ImageService::rules(),
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->only('name', 'location', 'pic_name', 'pic_phone');
        $data['code']      = strtoupper($request->code);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('photo')) {
            $data['photo'] = ImageService::upload($request->file('photo'), 'warehouses', $warehouse->photo);
        }

        $warehouse->update($data);

        return redirect()->route('warehouses.index')->with('success', 'Gudang berhasil diupdate.');
    }

    // ── DELETE /warehouses/{warehouse} ───────────────────────
    public function destroy(Warehouse $warehouse): RedirectResponse
    {
        $totalStock = $warehouse->stocks()->sum('quantity');
        if ($totalStock > 0) {
            return redirect()->back()->with('error', "Gudang tidak dapat dihapus karena masih memiliki {$totalStock} item stok.");
        }

        if ($warehouse->photo) {
            ImageService::delete($warehouse->photo);
        }

        $warehouse->delete();

        return redirect()->route('warehouses.index')->with('success', 'Gudang berhasil dihapus.');
    }
}
