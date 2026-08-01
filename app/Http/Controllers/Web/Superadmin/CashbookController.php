<?php

namespace App\Http\Controllers\Web\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\CashBook;
use Illuminate\Http\Request;

class CashBookController extends Controller
{
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

        return view('superadmin.cashbook.index', compact('books', 'totalMasuk', 'totalKeluar'));
    }

    public function show(CashBook $cashBook)
    {
        $cashBook->load(['createdBy:id,name', 'payment']);
        return view('superadmin.cashbook.show', compact('cashBook'));
    }

    public function create()
    {
        return view('superadmin.cashbook.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'type'        => 'required|in:masuk,keluar',
            'pihak'       => 'required|string|max:255',
            'jumlah_uang' => 'required|numeric|min:0',
            'terbilang'   => 'required|string',
            'keterangan'  => 'nullable|string',
            'tanggal'     => 'required|date',
        ]);

        $count    = CashBook::whereYear('created_at', now()->year)->count() + 1;
        $noBukti  = 'CB/MAN/' . now()->format('Y') . '/' . str_pad($count, 4, '0', STR_PAD_LEFT);

        CashBook::create([
            'no_bukti'    => $noBukti,
            'created_by'  => auth()->id(),
            'type'        => $request->type,
            'pihak'       => $request->pihak,
            'jumlah_uang' => $request->jumlah_uang,
            'terbilang'   => $request->terbilang,
            'keterangan'  => $request->keterangan,
            'tanggal'     => $request->tanggal,
        ]);

        return redirect()->route('cash-books.index')
            ->with('success', 'Entri kas berhasil ditambahkan.');
    }

    public function edit(CashBook $cashBook)
    {
        return view('superadmin.cashbook.edit', compact('cashBook'));
    }

    public function update(Request $request, CashBook $cashBook)
    {
        $request->validate([
            'pihak'       => 'required|string|max:255',
            'jumlah_uang' => 'required|numeric|min:0',
            'terbilang'   => 'required|string',
            'keterangan'  => 'nullable|string',
            'tanggal'     => 'required|date',
        ]);

        $cashBook->update($request->only('pihak', 'jumlah_uang', 'terbilang', 'keterangan', 'tanggal'));

        return redirect()->route('cash-books.show', $cashBook)
            ->with('success', 'Entri kas berhasil diupdate.');
    }
}