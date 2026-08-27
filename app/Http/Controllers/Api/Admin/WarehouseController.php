<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Services\ImageService;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WarehouseController extends Controller
{
    // ── GET /api/warehouses ──────────────────────────────────
    public function index(Request $request): JsonResponse
    {
        $warehouses = Warehouse::withCount('stocks')
            ->when($request->search, fn($q) => $q->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('code', 'like', "%{$request->search}%")
                  ->orWhere('location', 'like', "%{$request->search}%");
            }))
            ->when(isset($request->is_active), fn($q) => $q->where('is_active', $request->boolean('is_active')))
            ->latest()
            ->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data'    => $warehouses,
        ]);
    }

    // ── GET /api/warehouses/{warehouse} ──────────────────────
    public function show(Warehouse $warehouse): JsonResponse
    {
        $warehouse->loadCount('stocks')
                  ->load(['stocks.product:id,name,sku,unit']);

        return response()->json([
            'success' => true,
            'data'    => $warehouse,
        ]);
    }

    // ── POST /api/warehouses ─────────────────────────────────
    public function store(Request $request): JsonResponse
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
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = ImageService::upload($request->file('photo'), 'warehouses');
        }

        $warehouse = Warehouse::create([
            'name'      => $request->name,
            'code'      => strtoupper($request->code),
            'location'  => $request->location,
            'pic_name'  => $request->pic_name,
            'pic_phone' => $request->pic_phone,
            'is_active' => $request->boolean('is_active', true),
            'photo'     => $photoPath,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Gudang berhasil dibuat.',
            'data'    => $warehouse,
        ], 201);
    }

    // ── PUT /api/warehouses/{warehouse} ──────────────────────
    public function update(Request $request, Warehouse $warehouse): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'sometimes|required|string|max:255',
            'code'      => ['sometimes', 'required', 'string', 'max:50',
                            Rule::unique('warehouses')->ignore($warehouse->id)],
            'location'  => 'sometimes|required|string',
            'pic_name'  => 'nullable|string|max:255',
            'pic_phone' => 'nullable|string|max:20',
            'is_active' => 'nullable|boolean',
            'photo'     => ImageService::rules(),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $data = $request->only('name', 'location', 'pic_name', 'pic_phone');

        if ($request->has('code')) {
            $data['code'] = strtoupper($request->code);
        }

        if ($request->has('is_active')) {
            $data['is_active'] = $request->boolean('is_active');
        }

        if ($request->hasFile('photo')) {
            $data['photo'] = ImageService::upload(
                $request->file('photo'),
                'warehouses',
                $warehouse->photo
            );
        }

        $warehouse->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Gudang berhasil diupdate.',
            'data'    => $warehouse->fresh(),
        ]);
    }

    // ── DELETE /api/warehouses/{warehouse} ───────────────────
    public function destroy(Warehouse $warehouse): JsonResponse
    {
        // Cek apakah masih ada stok aktif
        $totalStock = $warehouse->stocks()->sum('quantity');
        if ($totalStock > 0) {
            return response()->json([
                'success' => false,
                'message' => "Gudang tidak dapat dihapus karena masih memiliki {$totalStock} item stok.",
            ], 422);
        }

        if ($warehouse->photo) {
            ImageService::delete($warehouse->photo);
        }

        $warehouse->delete();
        return response()->json([
            'success' => true,
            'message' => 'Gudang berhasil dihapus.',
        ]);
    }
}