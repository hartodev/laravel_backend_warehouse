<?php

namespace App\Http\Controllers\Web\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\CashBook;
use App\Models\Payment;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $payments = Payment::with(['createdBy:id,name', 'verifiedBy:id,name'])
            ->when($request->payment_type, fn($q) => $q->where('payment_type', $request->payment_type))
            ->when($request->payment_method, fn($q) => $q->where('payment_method', $request->payment_method))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->date_from, fn($q) => $q->whereDate('payment_date', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('payment_date', '<=', $request->date_to))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('superadmin.payment.index', compact('payments'));
    }

    public function create()
    {
        return view('superadmin.payment.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'payment_type'       => 'required|in:masuk,keluar',
            'payment_method'     => 'required|in:cash,transfer,cek,giro',
            'nominal'            => 'required|numeric|min:0',
            'payment_date'       => 'required|date',
            'purchase_order_id'  => 'nullable|exists:purchase_orders,id',
            'sales_order_id'     => 'nullable|exists:sales_orders,id',
            'budget_request_id'  => 'nullable|exists:budget_requests,id',
            'diterima_dari'      => 'nullable|string|max:255',
            'untuk_pembayaran'   => 'nullable|string',
            'terbilang'          => 'nullable|string',
            'nama_pengirim'      => 'nullable|string|max:255',
            'bank_pengirim'      => 'nullable|string|max:100',
            'nama_penerima'      => 'nullable|string|max:255',
            'bank_penerima'      => 'nullable|string|max:100',
            'no_rekening_tujuan' => 'nullable|string|max:50',
            'keterangan'         => 'nullable|string',
            'bukti_file'         => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $payment = null;
        DB::transaction(function () use ($request, &$payment) {
            $count  = Payment::whereYear('created_at', now()->year)->count() + 1;
            $number = 'PAY/' . now()->format('Y') . '/' . str_pad($count, 4, '0', STR_PAD_LEFT);

            $buktiPath = null;
            if ($request->hasFile('bukti_file')) {
                $buktiPath = ImageService::upload($request->file('bukti_file'), 'payments');
            }

            $payment = Payment::create([
                'payment_number'     => $number,
                'created_by'         => auth()->id(),
                'payment_type'       => $request->payment_type,
                'payment_method'     => $request->payment_method,
                'nominal'            => $request->nominal,
                'payment_date'       => $request->payment_date,
                'purchase_order_id'  => $request->purchase_order_id,
                'sales_order_id'     => $request->sales_order_id,
                'budget_request_id'  => $request->budget_request_id,
                'diterima_dari'      => $request->diterima_dari,
                'untuk_pembayaran'   => $request->untuk_pembayaran,
                'terbilang'          => $request->terbilang,
                'nama_pengirim'      => $request->nama_pengirim,
                'bank_pengirim'      => $request->bank_pengirim,
                'nama_penerima'      => $request->nama_penerima,
                'bank_penerima'      => $request->bank_penerima,
                'no_rekening_tujuan' => $request->no_rekening_tujuan,
                'keterangan'         => $request->keterangan,
                'bukti_file'         => $buktiPath,
                'status'             => 'pending',
            ]);

            CashBook::create([
                'no_bukti'    => 'CB-' . $number,
                'payment_id'  => $payment->id,
                'created_by'  => auth()->id(),
                'type'        => $request->payment_type,
                'pihak'       => $request->diterima_dari ?? $request->nama_pengirim ?? '-',
                'jumlah_uang' => $request->nominal,
                'terbilang'   => $request->terbilang ?? '-',
                'keterangan'  => $request->keterangan,
                'tanggal'     => $request->payment_date,
            ]);
        });

        return redirect()->route('superadmin.payments.show', $payment)
            ->with('success', 'Pembayaran berhasil dicatat.');
    }

    public function show(Payment $payment)
    {
        $payment->load(['createdBy:id,name', 'verifiedBy:id,name', 'purchaseOrder:id,po_number', 'salesOrder:id,so_number', 'budgetRequest:id,nomor_form', 'cashBook']);
        return view('superadmin.payment.show', compact('payment'));
    }

    public function edit(Payment $payment)
    {
        if ($payment->status === 'verified') {
            return back()->with('error', 'Pembayaran yang sudah diverifikasi tidak dapat diubah.');
        }
        return view('superadmin.payment.edit', compact('payment'));
    }

    public function update(Request $request, Payment $payment)
    {
        if ($payment->status === 'verified') {
            return back()->with('error', 'Pembayaran yang sudah diverifikasi tidak dapat diubah.');
        }

        $buktiPath = $payment->bukti_file;
        if ($request->hasFile('bukti_file')) {
            $buktiPath = ImageService::upload($request->file('bukti_file'), 'payments', $payment->bukti_file);
        }

        $payment->update(array_merge(
            $request->only('nominal', 'payment_date', 'diterima_dari', 'untuk_pembayaran', 'terbilang', 'nama_pengirim', 'bank_pengirim', 'nama_penerima', 'bank_penerima', 'no_rekening_tujuan', 'keterangan'),
            ['bukti_file' => $buktiPath]
        ));

        return redirect()->route('superadmin.payments.show', $payment)
            ->with('success', 'Pembayaran berhasil diupdate.');
    }

    public function destroy(Payment $payment)
    {
        if ($payment->status === 'verified') {
            return back()->with('error', 'Pembayaran yang sudah diverifikasi tidak dapat dihapus.');
        }

        if ($payment->bukti_file) ImageService::delete($payment->bukti_file);
        $payment->cashBook?->delete();
        $payment->delete();

        return redirect()->route('superadmin.payments.index')
            ->with('success', 'Pembayaran berhasil dihapus.');
    }

    public function verify(Request $request, Payment $payment)
    {
        if ($payment->status !== 'pending') {
            return back()->with('error', 'Hanya pembayaran pending yang dapat diverifikasi.');
        }

        $payment->update(['status' => 'verified', 'verified_by' => auth()->id(), 'verified_at' => now()]);

        return back()->with('success', 'Pembayaran berhasil diverifikasi.');
    }
}


