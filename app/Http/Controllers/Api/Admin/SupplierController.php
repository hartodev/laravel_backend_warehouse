<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
 public function index(Request $request): JsonResponse
    {
        $suppliers = Supplier::withCount('purchaseOrders')
            ->when($request->search, fn($q) => $q->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('code', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            }))
            ->when(isset($request->is_active), fn($q) => $q->where('is_active', $request->boolean('is_active')))
            ->latest()
            ->paginate($request->per_page ?? 15);

        return response()->json(['success' => true, 'data' => $suppliers]);
    }

    public function show(Supplier $supplier): JsonResponse
    {
        $supplier->loadCount('purchaseOrders');

        return response()->json(['success' => true, 'data' => $supplier]);
    }

    public function store(Request $request): JsonResponse
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
            return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }

        $supplier = Supplier::create(array_merge(
            $request->only('name', 'contact_person', 'phone', 'email', 'address', 'city', 'province', 'npwp', 'bank_name', 'bank_account', 'bank_account_name', 'notes'),
            [
                'code'      => strtoupper($request->code),
                'is_active' => $request->boolean('is_active', true),
            ]
        ));

        return response()->json(['success' => true, 'message' => 'Supplier berhasil dibuat.', 'data' => $supplier], 201);
    }

    public function update(Request $request, Supplier $supplier): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'              => 'sometimes|required|string|max:255',
            'code'              => ['sometimes', 'required', 'string', 'max:50', Rule::unique('suppliers')->ignore($supplier->id)],
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
            return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }

        $data = $request->only('name', 'contact_person', 'phone', 'email', 'address', 'city', 'province', 'npwp', 'bank_name', 'bank_account', 'bank_account_name', 'notes');

        if ($request->has('code')) $data['code'] = strtoupper($request->code);
        if ($request->has('is_active')) $data['is_active'] = $request->boolean('is_active');

        $supplier->update($data);

        return response()->json(['success' => true, 'message' => 'Supplier berhasil diupdate.', 'data' => $supplier->fresh()]);
    }

    public function destroy(Supplier $supplier): JsonResponse
    {
        if ($supplier->purchaseOrders()->exists()) {
            return response()->json(['success' => false, 'message' => 'Supplier tidak dapat dihapus karena masih memiliki purchase order.'], 422);
        }

        $supplier->delete();

        return response()->json(['success' => true, 'message' => 'Supplier berhasil dihapus.']);
    }
}

