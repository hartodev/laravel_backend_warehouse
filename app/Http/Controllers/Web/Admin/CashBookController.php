<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashBook;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CashBookController extends Controller
{
    public function index(Request $request): View
    {
        $books = CashBook::with(['createdBy:id,name', 'verifiedBy:id,name', 'payment:id,payment_number'])
            ->when($request->type, fn($q) => $q->where('type', $request->type))
            ->when($request->date_from, fn($q) => $q->whereDate('tanggal', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('tanggal', '<=', $request->date_to))
            ->orderByDesc('tanggal')
            ->paginate(20)
            ->withQueryString();

        $totalMasuk  = CashBook::where('type', 'masuk')
            ->when($request->date_from, fn($q) => $q->whereDate('tanggal', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('tanggal', '<=', $request->date_to))
            ->sum('jumlah_uang');

        $totalKeluar = CashBook::where('type', 'keluar')
            ->when($request->date_from, fn($q) => $q->whereDate('tanggal', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('tanggal', '<=', $request->date_to))
            ->sum('jumlah_uang');

        return view('Admin.cashbook.index', compact('books', 'totalMasuk', 'totalKeluar'));
    }

    public function create(): View
    {
        return view('Admin.cashbook.create');
    }

    public function show(CashBook $book): View
    {
        $book->load(['createdBy:id,name', 'verifiedBy:id,name', 'payment']);

        return view('Admin.cashbook.show', compact('book'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type'        => 'required|in:masuk,keluar',
            'pihak'       => 'required|string|max:255',
            'jumlah_uang' => 'required|numeric|min:0',
            'terbilang'   => 'required|string',
            'keterangan'  => 'nullable|string',
            'tanggal'     => 'required|date',
        ]);

        $count  = CashBook::whereYear('created_at', now()->year)->count() + 1;
        $number = ($validated['type'] === 'masuk' ? 'KM' : 'KK') . '/' . now()->format('Y') . '/' . str_pad($count, 4, '0', STR_PAD_LEFT);

        $book = CashBook::create([
            'no_bukti'    => $number,
            'created_by'  => auth()->id(),
            'type'        => $validated['type'],
            'pihak'       => $validated['pihak'],
            'jumlah_uang' => $validated['jumlah_uang'],
            'terbilang'   => $validated['terbilang'],
            'keterangan'  => $validated['keterangan'] ?? null,
            'tanggal'     => $validated['tanggal'],
        ]);

        return redirect()->route('admin.cashbook.show', $book)
            ->with('success', 'Buku kas berhasil dicatat.');
    }

    public function update(Request $request, CashBook $book): RedirectResponse
    {
        if ($book->verified_at) {
            return back()->with('error', 'Buku kas yang sudah diverifikasi tidak dapat diubah.');
        }

        $validated = $request->validate([
            'pihak'       => 'sometimes|required|string|max:255',
            'jumlah_uang' => 'sometimes|required|numeric|min:0',
            'terbilang'   => 'sometimes|required|string',
            'keterangan'  => 'nullable|string',
            'tanggal'     => 'sometimes|required|date',
        ]);

        $book->update($validated);

        return redirect()->route('admin.cashbook.show', $book)
            ->with('success', 'Buku kas berhasil diupdate.');
    }
}