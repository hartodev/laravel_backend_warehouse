<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashBook;
use Illuminate\Http\Request;

class CashBookController extends Controller
{
    /**
     * Read-only untuk Admin — entri kas (create/edit) tetap wewenang
     * Superadmin. Kalau nanti Admin perlu bisa input juga, tambah
     * method create/store di sini + view Admin.cashbook.create.
     */
    public function index(Request $request)
    {
        $books = CashBook::with(['createdBy:id,name', 'payment:id,payment_number'])
            ->when($request->type, fn($q) => $q->where('type', $request->type))
            ->when($request->date_from, fn($q) => $q->whereDate('tanggal', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('tanggal', '<=', $request->date_to))
            ->orderByDesc('tanggal')
            ->paginate(20)
            ->withQueryString();

        $totalMasuk  = CashBook::where('type', 'masuk')->sum('jumlah_uang');
        $totalKeluar = CashBook::where('type', 'keluar')->sum('jumlah_uang');

        return view('Admin.cashbook.index', compact('books', 'totalMasuk', 'totalKeluar'));
    }
}