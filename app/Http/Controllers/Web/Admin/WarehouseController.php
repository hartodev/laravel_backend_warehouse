<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\View\View;

class WarehouseController extends Controller
{
    public function index(Request $request): View
    {
        $warehouses = Warehouse::withCount('stocks')
            ->when($request->search, fn($q) => $q->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('code', 'like', "%{$request->search}%")
                  ->orWhere('location', 'like', "%{$request->search}%");
            }))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.warehouses.index', compact('warehouses'));
    }

    public function create(): View
    {
        return view('admin.warehouses.create');
    }

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
            return back()->withErrors($validator)->withInput();
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

        return redirect()
            ->route('admin.warehouses.index')
            ->with('success', 'Gudang berhasil dibuat.');
    }

    public function edit(Warehouse $warehouse): View
    {
        return view('admin.warehouses.edit', compact('warehouse'));
    }

    public function update(Request $request, Warehouse $warehouse): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'sometimes|required|string|max:255',
            'code'      => ['sometimes', 'required', 'string', 'max:50', Rule::unique('warehouses')->ignore($warehouse->id)],
            'location'  => 'sometimes|required|string',
            'pic_name'  => 'nullable|string|max:255',
            'pic_phone' => 'nullable|string|max:20',
            'is_active' => 'nullable|boolean',
            'photo'     => ImageService::rules(),
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->only('name', 'location', 'pic_name', 'pic_phone');

        if ($request->has('code')) {
            $data['code'] = strtoupper($request->code);
        }
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('photo')) {
            $data['photo'] = ImageService::upload($request->file('photo'), 'warehouses', $warehouse->photo);
        }

        $warehouse->update($data);

        return redirect()
            ->route('admin.warehouses.index')
            ->with('success', 'Gudang berhasil diupdate.');
    }

    public function destroy(Warehouse $warehouse): RedirectResponse
    {
        $totalStock = $warehouse->stocks()->sum('quantity');
        if ($totalStock > 0) {
            return back()->with('error', "Gudang tidak dapat dihapus karena masih memiliki {$totalStock} item stok.");
        }

        if ($warehouse->photo) {
            ImageService::delete($warehouse->photo);
        }

        $warehouse->delete();

        return redirect()
            ->route('admin.warehouses.index')
            ->with('success', 'Gudang berhasil dihapus.');
    }
}