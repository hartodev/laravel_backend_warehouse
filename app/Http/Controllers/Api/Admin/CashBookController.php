<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashBook;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CashBookController extends Controller
{
   public function index(Request $request): JsonResponse
    {
        $books = CashBook::with(['createdBy:id,name', 'verifiedBy:id,name', 'payment:id,payment_number'])
            ->when($request->type, fn($q) => $q->where('type', $request->type))
            ->when($request->date_from, fn($q) => $q->whereDate('tanggal', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('tanggal', '<=', $request->date_to))
            ->orderByDesc('tanggal')
            ->paginate($request->per_page ?? 20);
 
        // Hitung total masuk & keluar
        $totalMasuk  = CashBook::where('type', 'masuk')
            ->when($request->date_from, fn($q) => $q->whereDate('tanggal', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('tanggal', '<=', $request->date_to))
            ->sum('jumlah_uang');
 
        $totalKeluar = CashBook::where('type', 'keluar')
            ->when($request->date_from, fn($q) => $q->whereDate('tanggal', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('tanggal', '<=', $request->date_to))
            ->sum('jumlah_uang');
 
        return response()->json([
            'success' => true,
            'summary' => [
                'total_masuk'  => $totalMasuk,
                'total_keluar' => $totalKeluar,
                'saldo'        => $totalMasuk - $totalKeluar,
            ],
            'data' => $books,
        ]);
    }
 
    public function show(CashBook $book): JsonResponse
    {
        $book->load(['createdBy:id,name', 'verifiedBy:id,name', 'payment']);
 
        return response()->json(['success' => true, 'data' => $book]);
    }
 
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type'        => 'required|in:masuk,keluar',
            'pihak'       => 'required|string|max:255',
            'jumlah_uang' => 'required|numeric|min:0',
            'terbilang'   => 'required|string',
            'keterangan'  => 'nullable|string',
            'tanggal'     => 'required|date',
        ]);
 
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }
 
        $count  = CashBook::whereYear('created_at', now()->year)->count() + 1;
        $number = ($request->type === 'masuk' ? 'KM' : 'KK') . '/' . now()->format('Y') . '/' . str_pad($count, 4, '0', STR_PAD_LEFT);
 
        $book = CashBook::create([
            'no_bukti'    => $number,
            'created_by'  => auth()->id(),
            'type'        => $request->type,
            'pihak'       => $request->pihak,
            'jumlah_uang' => $request->jumlah_uang,
            'terbilang'   => $request->terbilang,
            'keterangan'  => $request->keterangan,
            'tanggal'     => $request->tanggal,
        ]);
 
        return response()->json(['success' => true, 'message' => 'Buku kas berhasil dicatat.', 'data' => $book], 201);
    }
 
    public function update(Request $request, CashBook $book): JsonResponse
    {
        if ($book->verified_at) {
            return response()->json(['success' => false, 'message' => 'Buku kas yang sudah diverifikasi tidak dapat diubah.'], 422);
        }
 
        $book->update($request->only('pihak', 'jumlah_uang', 'terbilang', 'keterangan', 'tanggal'));
 
        return response()->json(['success' => true, 'message' => 'Buku kas berhasil diupdate.', 'data' => $book->fresh()]);
    }
}