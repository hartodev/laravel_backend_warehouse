<?php

namespace App\Services;

use App\Models\BudgetRequest;
use App\Models\BudgetRevision;
use App\Models\BudgetVerification;
use App\Models\CashBook;
use App\Models\ExpenseReport;
use App\Models\Payment;
use App\Models\ProductSubmission;
use App\Models\PurchaseOrder;
use App\Models\SalesOrder;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\StockOpname;
use App\Models\StockReport;
use App\Models\StockTransfer;
use App\Models\Warehouse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * ReportService
 * -------------
 * Satu tempat untuk menyiapkan data "Generate Laporan" dari semua modul
 * (gudang, stock, mutasi stock, transfer stock, stock opname, laporan
 * stock, pengajuan product, purchase order, sales order, pembayaran,
 * kasbook, review rab masuk, verifikasi finance, revisi anggaran,
 * laporan realisasi).
 *
 * Dipakai bersama oleh Admin\ReportController & Superadmin\ReportController
 * supaya logikanya tidak dobel. Setiap method mengembalikan array standar:
 *   [
 *     'title'   => string,
 *     'columns' => ['Label Kolom' => 'key'],
 *     'rows'    => array of assoc array (key sesuai 'columns'),
 *     'summary' => array of ['label' => .., 'value' => ..] (opsional),
 *   ]
 */
class ReportService
{
    /**
     * Daftar semua jenis laporan yang tersedia: key => label.
     * Dipakai untuk membangun menu "Pusat Laporan" & validasi route.
     */
    public static function available(): array
    {
        return [
            'warehouses'           => 'Data Gudang',
            'stocks'               => 'Stock Barang',
            'stock-movements'      => 'Mutasi Stock',
            'stock-transfers'      => 'Transfer Stock',
            'stock-opnames'        => 'Stock Opname',
            'stock-report'         => 'Laporan Stock (Kartu Stok)',
            'product-submissions'  => 'Pengajuan Produk',
            'purchase-orders'      => 'Purchase Order',
            'sales-orders'         => 'Sales Order',
            'payments'             => 'Pembayaran',
            'cashbook'             => 'Kasbook (Buku Kas)',
            'budget-requests'      => 'Review RAB Masuk',
            'budget-verifications' => 'Verifikasi Finance',
            'budget-revisions'     => 'Revisi Anggaran',
            'expense-reports'      => 'Laporan Realisasi',
        ];
    }

    /** Titik masuk utama: generate($type, $filters) */
    public static function generate(string $type, array $filters): array
    {
        $method = Str::camel($type);

        if (!method_exists(self::class, $method)) {
            abort(404, 'Jenis laporan tidak dikenali.');
        }

        return self::$method($filters);
    }

    private static function dateFrom(array $f)
    {
        return $f['date_from'] ?? null;
    }

    private static function dateTo(array $f)
    {
        return $f['date_to'] ?? null;
    }

    private static function rp($value): string
    {
        return 'Rp ' . number_format((float) $value, 0, ',', '.');
    }

    // ── 1. Gudang ────────────────────────────────────────────────
    public static function warehouses(array $f): array
    {
        $rows = Warehouse::query()
            ->withCount(['stocks', 'stockMovements'])
            ->when($f['status'] ?? null, fn($q, $v) => $q->where('is_active', $v === 'active'))
            ->when($f['search'] ?? null, fn($q, $v) => $q->where(function ($q) use ($v) {
                $q->where('name', 'like', "%{$v}%")->orWhere('code', 'like', "%{$v}%");
            }))
            ->orderBy('name')
            ->get();

        return [
            'title' => 'Laporan Data Gudang',
            'columns' => [
                'Kode' => 'code', 'Nama Gudang' => 'name', 'Kota' => 'city',
                'PIC' => 'pic_name', 'Telp PIC' => 'pic_phone',
                'Jml Item Stok' => 'stocks_count', 'Status' => 'status',
            ],
            'rows' => $rows->map(fn($w) => [
                'code' => $w->code, 'name' => $w->name, 'city' => $w->city ?? '-',
                'pic_name' => $w->pic_name ?? '-', 'pic_phone' => $w->pic_phone ?? '-',
                'stocks_count' => $w->stocks_count,
                'status' => $w->is_active ? 'Aktif' : 'Nonaktif',
            ])->all(),
            'summary' => [
                ['label' => 'Total Gudang', 'value' => $rows->count()],
                ['label' => 'Gudang Aktif', 'value' => $rows->where('is_active', true)->count()],
            ],
        ];
    }

    // ── 2. Stock barang per gudang ──────────────────────────────
    public static function stocks(array $f): array
    {
        $rows = Stock::with(['product:id,name,sku,unit,min_stock', 'warehouse:id,name,code'])
            ->when($f['warehouse_id'] ?? null, fn($q, $v) => $q->where('warehouse_id', $v))
            ->when($f['search'] ?? null, fn($q, $v) => $q->whereHas('product', fn($q2) => $q2
                ->where('name', 'like', "%{$v}%")->orWhere('sku', 'like', "%{$v}%")))
            ->get();

        return [
            'title' => 'Laporan Stock Barang',
            'columns' => [
                'Gudang' => 'warehouse', 'SKU' => 'sku', 'Produk' => 'product',
                'Satuan' => 'unit', 'Qty' => 'qty', 'Min. Stok' => 'min_stock', 'Status' => 'status',
            ],
            'rows' => $rows->map(fn($s) => [
                'warehouse' => $s->warehouse->name ?? '-',
                'sku' => $s->product->sku ?? '-',
                'product' => $s->product->name ?? '-',
                'unit' => $s->product->unit ?? '-',
                'qty' => $s->quantity,
                'min_stock' => $s->product->min_stock ?? 0,
                'status' => $s->quantity <= ($s->product->min_stock ?? 0) ? 'Stok Rendah' : 'Aman',
            ])->all(),
            'summary' => [
                ['label' => 'Total Item', 'value' => $rows->count()],
                ['label' => 'Total Qty', 'value' => number_format($rows->sum('quantity'), 0, ',', '.')],
                ['label' => 'Item Stok Rendah', 'value' => $rows->filter(fn($s) => $s->quantity <= ($s->product->min_stock ?? 0))->count()],
            ],
        ];
    }

    // ── 3. Mutasi Stock ──────────────────────────────────────────
    public static function stockMovements(array $f): array
    {
        $rows = StockMovement::with(['product:id,name,sku', 'warehouse:id,name', 'createdBy:id,name'])
            ->when($f['warehouse_id'] ?? null, fn($q, $v) => $q->where('warehouse_id', $v))
            ->when($f['type'] ?? null, fn($q, $v) => $q->where('type', $v))
            ->when(self::dateFrom($f), fn($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when(self::dateTo($f), fn($q, $v) => $q->whereDate('created_at', '<=', $v))
            ->orderByDesc('created_at')
            ->get();

        $labels = [
            'in' => 'Masuk', 'out' => 'Keluar', 'transfer_in' => 'Transfer Masuk',
            'transfer_out' => 'Transfer Keluar', 'adjustment' => 'Penyesuaian',
        ];

        return [
            'title' => 'Laporan Mutasi Stock',
            'columns' => [
                'Tanggal' => 'date', 'Gudang' => 'warehouse', 'Produk' => 'product',
                'Jenis' => 'type', 'Qty' => 'qty', 'Sebelum' => 'before',
                'Sesudah' => 'after', 'Oleh' => 'by', 'Catatan' => 'note',
            ],
            'rows' => $rows->map(fn($m) => [
                'date' => optional($m->created_at)->format('d/m/Y H:i'),
                'warehouse' => $m->warehouse->name ?? '-',
                'product' => $m->product->name ?? '-',
                'type' => $labels[$m->type] ?? $m->type,
                'qty' => ($m->quantity > 0 ? '+' : '') . $m->quantity,
                'before' => $m->quantity_before,
                'after' => $m->quantity_after,
                'by' => $m->createdBy->name ?? '-',
                'note' => $m->note ?? '-',
            ])->all(),
            'summary' => [
                ['label' => 'Total Transaksi', 'value' => $rows->count()],
                ['label' => 'Total Masuk', 'value' => $rows->where('quantity', '>', 0)->sum('quantity')],
                ['label' => 'Total Keluar', 'value' => abs($rows->where('quantity', '<', 0)->sum('quantity'))],
            ],
        ];
    }

    // ── 4. Transfer Stock ────────────────────────────────────────
    public static function stockTransfers(array $f): array
    {
        $rows = StockTransfer::with(['fromWarehouse:id,name', 'toWarehouse:id,name', 'requestedBy:id,name', 'items'])
            ->when($f['status'] ?? null, fn($q, $v) => $q->where('status', $v))
            ->when(self::dateFrom($f), fn($q, $v) => $q->whereDate('transfer_date', '>=', $v))
            ->when(self::dateTo($f), fn($q, $v) => $q->whereDate('transfer_date', '<=', $v))
            ->orderByDesc('transfer_date')
            ->get();

        $statusLabel = [
            'pending' => 'Menunggu', 'approved' => 'Disetujui', 'in_transit' => 'Dalam Perjalanan',
            'received' => 'Diterima', 'rejected' => 'Ditolak', 'cancelled' => 'Dibatalkan',
        ];

        return [
            'title' => 'Laporan Transfer Stock',
            'columns' => [
                'No. Transfer' => 'number', 'Tanggal' => 'date', 'Dari' => 'from',
                'Ke' => 'to', 'Diminta Oleh' => 'by', 'Jml Item' => 'items', 'Status' => 'status',
            ],
            'rows' => $rows->map(fn($t) => [
                'number' => $t->transfer_number,
                'date' => optional($t->transfer_date)->format('d/m/Y'),
                'from' => $t->fromWarehouse->name ?? '-',
                'to' => $t->toWarehouse->name ?? '-',
                'by' => $t->requestedBy->name ?? '-',
                'items' => $t->items->count(),
                'status' => $statusLabel[$t->status] ?? $t->status,
            ])->all(),
            'summary' => [
                ['label' => 'Total Transfer', 'value' => $rows->count()],
                ['label' => 'Selesai (Diterima)', 'value' => $rows->where('status', 'received')->count()],
                ['label' => 'Menunggu/Proses', 'value' => $rows->whereIn('status', ['pending', 'approved', 'in_transit'])->count()],
            ],
        ];
    }

    // ── 5. Stock Opname ───────────────────────────────────────────
    public static function stockOpnames(array $f): array
    {
        $rows = StockOpname::with(['warehouse:id,name', 'createdBy:id,name', 'items'])
            ->when($f['warehouse_id'] ?? null, fn($q, $v) => $q->where('warehouse_id', $v))
            ->when($f['status'] ?? null, fn($q, $v) => $q->where('status', $v))
            ->when(self::dateFrom($f), fn($q, $v) => $q->whereDate('opname_date', '>=', $v))
            ->when(self::dateTo($f), fn($q, $v) => $q->whereDate('opname_date', '<=', $v))
            ->orderByDesc('opname_date')
            ->get();

        $statusLabel = [
            'draft' => 'Draft', 'in_progress' => 'Berjalan', 'pending_approval' => 'Menunggu Persetujuan',
            'approved' => 'Disetujui', 'cancelled' => 'Dibatalkan',
        ];

        return [
            'title' => 'Laporan Stock Opname',
            'columns' => [
                'No. Opname' => 'number', 'Tanggal' => 'date', 'Gudang' => 'warehouse',
                'Dibuat Oleh' => 'by', 'Jml Item' => 'items', 'Total Selisih' => 'diff', 'Status' => 'status',
            ],
            'rows' => $rows->map(fn($o) => [
                'number' => $o->opname_number,
                'date' => optional($o->opname_date)->format('d/m/Y'),
                'warehouse' => $o->warehouse->name ?? '-',
                'by' => $o->createdBy->name ?? '-',
                'items' => $o->items->count(),
                'diff' => $o->items->sum('difference'),
                'status' => $statusLabel[$o->status] ?? $o->status,
            ])->all(),
            'summary' => [
                ['label' => 'Total Opname', 'value' => $rows->count()],
                ['label' => 'Disetujui', 'value' => $rows->where('status', 'approved')->count()],
            ],
        ];
    }

    // ── 6. Laporan Stock (kartu stok periodik) ───────────────────
    public static function stockReport(array $f): array
    {
        $rows = StockReport::with(['product:id,name,sku', 'warehouse:id,name'])
            ->when($f['warehouse_id'] ?? null, fn($q, $v) => $q->where('warehouse_id', $v))
            ->when($f['period_type'] ?? null, fn($q, $v) => $q->where('period_type', $v))
            ->when(self::dateFrom($f), fn($q, $v) => $q->where('period_date', '>=', $v))
            ->when(self::dateTo($f), fn($q, $v) => $q->where('period_date', '<=', $v))
            ->orderByDesc('period_date')
            ->get();

        return [
            'title' => 'Laporan Stock (Kartu Stok)',
            'columns' => [
                'Periode' => 'period', 'Gudang' => 'warehouse', 'Produk' => 'product',
                'Stok Awal' => 'opening', 'Masuk' => 'in', 'Keluar' => 'out',
                'Transfer Masuk' => 'trin', 'Transfer Keluar' => 'trout',
                'Penyesuaian' => 'adj', 'Stok Akhir' => 'closing', 'Nilai' => 'value',
            ],
            'rows' => $rows->map(fn($r) => [
                'period' => optional($r->period_date)->format('d/m/Y') . ' (' . $r->period_type . ')',
                'warehouse' => $r->warehouse->name ?? '-',
                'product' => $r->product->name ?? '-',
                'opening' => $r->opening_stock, 'in' => $r->stock_in, 'out' => $r->stock_out,
                'trin' => $r->transfer_in, 'trout' => $r->transfer_out, 'adj' => $r->adjustment,
                'closing' => $r->closing_stock, 'value' => self::rp($r->total_value),
            ])->all(),
            'summary' => [
                ['label' => 'Total Baris', 'value' => $rows->count()],
                ['label' => 'Total Nilai Stok', 'value' => self::rp($rows->sum('total_value'))],
            ],
        ];
    }

    // ── 7. Pengajuan Produk ───────────────────────────────────────
    public static function productSubmissions(array $f): array
    {
        $rows = ProductSubmission::with(['admin:id,name', 'category:id,name', 'initialWarehouse:id,name'])
            ->when($f['status'] ?? null, fn($q, $v) => $q->where('status', $v))
            ->when(self::dateFrom($f), fn($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when(self::dateTo($f), fn($q, $v) => $q->whereDate('created_at', '<=', $v))
            ->orderByDesc('created_at')
            ->get();

        $statusLabel = ['pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'];

        return [
            'title' => 'Laporan Pengajuan Produk',
            'columns' => [
                'Tanggal' => 'date', 'Nama Produk' => 'name', 'Kategori' => 'category',
                'Diajukan Oleh' => 'by', 'Stok Awal' => 'stock', 'Harga Beli' => 'buy',
                'Harga Jual' => 'sell', 'Status' => 'status',
            ],
            'rows' => $rows->map(fn($p) => [
                'date' => optional($p->created_at)->format('d/m/Y'),
                'name' => $p->name, 'category' => $p->category->name ?? '-',
                'by' => $p->admin->name ?? '-', 'stock' => $p->initial_stock,
                'buy' => self::rp($p->purchase_price), 'sell' => self::rp($p->selling_price),
                'status' => $statusLabel[$p->status] ?? $p->status,
            ])->all(),
            'summary' => [
                ['label' => 'Total Pengajuan', 'value' => $rows->count()],
                ['label' => 'Disetujui', 'value' => $rows->where('status', 'approved')->count()],
                ['label' => 'Ditolak', 'value' => $rows->where('status', 'rejected')->count()],
            ],
        ];
    }

    // ── 8. Purchase Order ─────────────────────────────────────────
    public static function purchaseOrders(array $f): array
    {
        $rows = PurchaseOrder::with(['supplier:id,name', 'warehouse:id,name'])
            ->when($f['status'] ?? null, fn($q, $v) => $q->where('status', $v))
            ->when($f['warehouse_id'] ?? null, fn($q, $v) => $q->where('warehouse_id', $v))
            ->when(self::dateFrom($f), fn($q, $v) => $q->whereDate('order_date', '>=', $v))
            ->when(self::dateTo($f), fn($q, $v) => $q->whereDate('order_date', '<=', $v))
            ->orderByDesc('order_date')
            ->get();

        $statusLabel = [
            'draft' => 'Draft', 'pending' => 'Menunggu', 'approved' => 'Disetujui',
            'partial' => 'Sebagian Diterima', 'received' => 'Diterima', 'cancelled' => 'Dibatalkan',
        ];

        return [
            'title' => 'Laporan Purchase Order',
            'columns' => [
                'No. PO' => 'number', 'Tanggal' => 'date', 'Supplier' => 'supplier',
                'Gudang' => 'warehouse', 'Total' => 'total', 'Status' => 'status',
            ],
            'rows' => $rows->map(fn($po) => [
                'number' => $po->po_number, 'date' => optional($po->order_date)->format('d/m/Y'),
                'supplier' => $po->supplier->name ?? '-', 'warehouse' => $po->warehouse->name ?? '-',
                'total' => self::rp($po->total_amount), 'status' => $statusLabel[$po->status] ?? $po->status,
            ])->all(),
            'summary' => [
                ['label' => 'Total PO', 'value' => $rows->count()],
                ['label' => 'Total Nilai', 'value' => self::rp($rows->sum('total_amount'))],
            ],
        ];
    }

    // ── 9. Sales Order ────────────────────────────────────────────
    public static function salesOrders(array $f): array
    {
        $rows = SalesOrder::with(['warehouse:id,name'])
            ->when($f['status'] ?? null, fn($q, $v) => $q->where('status', $v))
            ->when($f['warehouse_id'] ?? null, fn($q, $v) => $q->where('warehouse_id', $v))
            ->when(self::dateFrom($f), fn($q, $v) => $q->whereDate('order_date', '>=', $v))
            ->when(self::dateTo($f), fn($q, $v) => $q->whereDate('order_date', '<=', $v))
            ->orderByDesc('order_date')
            ->get();

        $statusLabel = [
            'draft' => 'Draft', 'confirmed' => 'Dikonfirmasi', 'processing' => 'Diproses',
            'shipped' => 'Dikirim', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan',
        ];

        return [
            'title' => 'Laporan Sales Order',
            'columns' => [
                'No. SO' => 'number', 'Tanggal' => 'date', 'Pelanggan' => 'customer',
                'Gudang' => 'warehouse', 'Total' => 'total', 'Status' => 'status',
            ],
            'rows' => $rows->map(fn($so) => [
                'number' => $so->so_number, 'date' => optional($so->order_date)->format('d/m/Y'),
                'customer' => $so->customer_name, 'warehouse' => $so->warehouse->name ?? '-',
                'total' => self::rp($so->total_amount), 'status' => $statusLabel[$so->status] ?? $so->status,
            ])->all(),
            'summary' => [
                ['label' => 'Total SO', 'value' => $rows->count()],
                ['label' => 'Total Penjualan', 'value' => self::rp($rows->sum('total_amount'))],
            ],
        ];
    }

    // ── 10. Pembayaran ────────────────────────────────────────────
    public static function payments(array $f): array
    {
        $rows = Payment::with(['createdBy:id,name', 'purchaseOrder:id,po_number', 'salesOrder:id,so_number'])
            ->when($f['status'] ?? null, fn($q, $v) => $q->where('status', $v))
            ->when($f['payment_type'] ?? null, fn($q, $v) => $q->where('payment_type', $v))
            ->when(self::dateFrom($f), fn($q, $v) => $q->whereDate('payment_date', '>=', $v))
            ->when(self::dateTo($f), fn($q, $v) => $q->whereDate('payment_date', '<=', $v))
            ->orderByDesc('payment_date')
            ->get();

        $statusLabel = ['pending' => 'Menunggu', 'verified' => 'Terverifikasi', 'cancelled' => 'Dibatalkan'];

        return [
            'title' => 'Laporan Pembayaran',
            'columns' => [
                'No. Pembayaran' => 'number', 'Tanggal' => 'date', 'Jenis' => 'type',
                'Referensi' => 'ref', 'Nominal' => 'nominal', 'Metode' => 'method', 'Status' => 'status',
            ],
            'rows' => $rows->map(fn($p) => [
                'number' => $p->payment_number, 'date' => optional($p->payment_date)->format('d/m/Y'),
                'type' => $p->payment_type === 'masuk' ? 'Masuk' : 'Keluar',
                'ref' => $p->purchaseOrder->po_number ?? $p->salesOrder->so_number ?? '-',
                'nominal' => self::rp($p->nominal), 'method' => strtoupper($p->payment_method),
                'status' => $statusLabel[$p->status] ?? $p->status,
            ])->all(),
            'summary' => [
                ['label' => 'Total Masuk', 'value' => self::rp($rows->where('payment_type', 'masuk')->sum('nominal'))],
                ['label' => 'Total Keluar', 'value' => self::rp($rows->where('payment_type', 'keluar')->sum('nominal'))],
            ],
        ];
    }

    // ── 11. Kasbook ───────────────────────────────────────────────
    public static function cashbook(array $f): array
    {
        $rows = CashBook::with(['createdBy:id,name'])
            ->when($f['type'] ?? null, fn($q, $v) => $q->where('type', $v))
            ->when(self::dateFrom($f), fn($q, $v) => $q->whereDate('tanggal', '>=', $v))
            ->when(self::dateTo($f), fn($q, $v) => $q->whereDate('tanggal', '<=', $v))
            ->orderBy('tanggal')
            ->get();

        $saldo = 0;
        $withSaldo = $rows->map(function ($c) use (&$saldo) {
            $saldo += $c->type === 'masuk' ? $c->jumlah_uang : -$c->jumlah_uang;
            return [
                'number' => $c->no_bukti, 'date' => optional($c->tanggal)->format('d/m/Y'),
                'type' => $c->type === 'masuk' ? 'Masuk' : 'Keluar', 'party' => $c->pihak,
                'desc' => $c->keterangan ?? '-', 'nominal' => self::rp($c->jumlah_uang),
                'saldo' => self::rp($saldo),
            ];
        });

        return [
            'title' => 'Laporan Kasbook (Buku Kas)',
            'columns' => [
                'No. Bukti' => 'number', 'Tanggal' => 'date', 'Jenis' => 'type',
                'Pihak' => 'party', 'Keterangan' => 'desc', 'Nominal' => 'nominal', 'Saldo' => 'saldo',
            ],
            'rows' => $withSaldo->all(),
            'summary' => [
                ['label' => 'Total Masuk', 'value' => self::rp($rows->where('type', 'masuk')->sum('jumlah_uang'))],
                ['label' => 'Total Keluar', 'value' => self::rp($rows->where('type', 'keluar')->sum('jumlah_uang'))],
                ['label' => 'Saldo Akhir', 'value' => self::rp($saldo)],
            ],
        ];
    }

    // ── 12. Review RAB Masuk (Budget Request) ─────────────────────
    public static function budgetRequests(array $f): array
    {
        $rows = BudgetRequest::with(['user:id,name'])
            ->when($f['status'] ?? null, fn($q, $v) => $q->where('status', $v))
            ->when($f['jenis'] ?? null, fn($q, $v) => $q->where('jenis', $v))
            ->when(self::dateFrom($f), fn($q, $v) => $q->whereDate('tanggal_pengajuan', '>=', $v))
            ->when(self::dateTo($f), fn($q, $v) => $q->whereDate('tanggal_pengajuan', '<=', $v))
            ->orderByDesc('tanggal_pengajuan')
            ->get();

        $statusLabel = [
            'draft' => 'Draft', 'pending' => 'Menunggu', 'pending_finance' => 'Menunggu Finance',
            'approved' => 'Disetujui', 'approved_revisi' => 'Disetujui (Revisi)',
            'ditunda' => 'Ditunda', 'ditolak' => 'Ditolak',
        ];

        return [
            'title' => 'Laporan Review RAB Masuk',
            'columns' => [
                'No. Form' => 'number', 'Tanggal' => 'date', 'Divisi' => 'div',
                'Jenis' => 'jenis', 'Pemohon' => 'by', 'Total Estimasi' => 'total', 'Status' => 'status',
            ],
            'rows' => $rows->map(fn($b) => [
                'number' => $b->nomor_form, 'date' => optional($b->tanggal_pengajuan)->format('d/m/Y'),
                'div' => $b->divisi, 'jenis' => $b->jenis === 'rab' ? 'RAB' : 'Luar RAB',
                'by' => $b->user->name ?? '-', 'total' => self::rp($b->total_estimasi),
                'status' => $statusLabel[$b->status] ?? $b->status,
            ])->all(),
            'summary' => [
                ['label' => 'Total Pengajuan', 'value' => $rows->count()],
                ['label' => 'Total Estimasi', 'value' => self::rp($rows->sum('total_estimasi'))],
                ['label' => 'Disetujui', 'value' => $rows->whereIn('status', ['approved', 'approved_revisi'])->count()],
            ],
        ];
    }

    // ── 13. Verifikasi Finance ─────────────────────────────────────
    public static function budgetVerifications(array $f): array
    {
        $rows = BudgetVerification::with(['budgetRequest:id,nomor_form', 'finance:id,name'])
            ->when($f['rekomendasi'] ?? null, fn($q, $v) => $q->where('rekomendasi', $v))
            ->when(self::dateFrom($f), fn($q, $v) => $q->whereDate('verified_at', '>=', $v))
            ->when(self::dateTo($f), fn($q, $v) => $q->whereDate('verified_at', '<=', $v))
            ->orderByDesc('verified_at')
            ->get();

        $recLabel = ['setuju' => 'Setuju', 'tunda' => 'Tunda', 'tolak' => 'Tolak'];

        return [
            'title' => 'Laporan Verifikasi Finance',
            'columns' => [
                'Tanggal' => 'date', 'No. Form RAB' => 'form', 'Verifikator' => 'by',
                'Dok. Lengkap' => 'doc', 'Rekomendasi' => 'rec', 'Nominal Rekomendasi' => 'nominal',
            ],
            'rows' => $rows->map(fn($v) => [
                'date' => optional($v->verified_at)->format('d/m/Y'),
                'form' => $v->budgetRequest->nomor_form ?? '-',
                'by' => $v->finance->name ?? '-',
                'doc' => ($v->doc_form_lengkap && $v->doc_surat_justifikasi) ? 'Lengkap' : 'Belum Lengkap',
                'rec' => $recLabel[$v->rekomendasi] ?? $v->rekomendasi,
                'nominal' => $v->nominal_rekomendasi ? self::rp($v->nominal_rekomendasi) : '-',
            ])->all(),
            'summary' => [
                ['label' => 'Total Verifikasi', 'value' => $rows->count()],
                ['label' => 'Disetujui', 'value' => $rows->where('rekomendasi', 'setuju')->count()],
                ['label' => 'Ditolak', 'value' => $rows->where('rekomendasi', 'tolak')->count()],
            ],
        ];
    }

    // ── 14. Revisi Anggaran ─────────────────────────────────────────
    public static function budgetRevisions(array $f): array
    {
        $rows = BudgetRevision::with(['createdBy:id,name', 'budgetRequest:id,nomor_form'])
            ->when($f['status'] ?? null, fn($q, $v) => $q->where('status', $v))
            ->when(self::dateFrom($f), fn($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when(self::dateTo($f), fn($q, $v) => $q->whereDate('created_at', '<=', $v))
            ->orderByDesc('created_at')
            ->get();

        $statusLabel = [
            'pending' => 'Menunggu', 'approved' => 'Disetujui', 'approved_revisi' => 'Disetujui (Revisi)',
            'ditunda' => 'Ditunda', 'ditolak' => 'Ditolak',
        ];

        return [
            'title' => 'Laporan Revisi Anggaran',
            'columns' => [
                'Tanggal' => 'date', 'Akun Terdampak' => 'account', 'Anggaran Awal' => 'before',
                'Jenis Perubahan' => 'change_type', 'Nominal Perubahan' => 'change',
                'Anggaran Baru' => 'after', 'Diajukan Oleh' => 'by', 'Status' => 'status',
            ],
            'rows' => $rows->map(fn($r) => [
                'date' => optional($r->created_at)->format('d/m/Y'),
                'account' => $r->akun_terdampak, 'before' => self::rp($r->anggaran_awal),
                'change_type' => $r->jenis_perubahan === 'tambahan' ? 'Tambahan' : 'Pengurangan',
                'change' => self::rp($r->nominal_perubahan), 'after' => self::rp($r->anggaran_baru),
                'by' => $r->createdBy->name ?? '-', 'status' => $statusLabel[$r->status] ?? $r->status,
            ])->all(),
            'summary' => [
                ['label' => 'Total Revisi', 'value' => $rows->count()],
                ['label' => 'Disetujui', 'value' => $rows->whereIn('status', ['approved', 'approved_revisi'])->count()],
            ],
        ];
    }

    // ── 15. Laporan Realisasi (Expense Report) ────────────────────
    public static function expenseReports(array $f): array
    {
        $rows = ExpenseReport::with(['budgetRequest:id,nomor_form', 'submittedBy:id,name'])
            ->when($f['status'] ?? null, fn($q, $v) => $q->where('status', $v))
            ->when(self::dateFrom($f), fn($q, $v) => $q->whereDate('tanggal_transaksi', '>=', $v))
            ->when(self::dateTo($f), fn($q, $v) => $q->whereDate('tanggal_transaksi', '<=', $v))
            ->orderByDesc('tanggal_transaksi')
            ->get();

        $statusLabel = ['draft' => 'Draft', 'submitted' => 'Diajukan', 'verified' => 'Terverifikasi', 'rejected' => 'Ditolak'];

        return [
            'title' => 'Laporan Realisasi Anggaran',
            'columns' => [
                'Tanggal' => 'date', 'No. Form RAB' => 'form', 'No. Invoice' => 'invoice',
                'Vendor' => 'vendor', 'Nominal Realisasi' => 'nominal', 'Selisih' => 'diff',
                'Pelapor' => 'by', 'Status' => 'status',
            ],
            'rows' => $rows->map(fn($e) => [
                'date' => optional($e->tanggal_transaksi)->format('d/m/Y'),
                'form' => $e->budgetRequest->nomor_form ?? '-', 'invoice' => $e->nomor_invoice ?? '-',
                'vendor' => $e->nama_vendor ?? '-', 'nominal' => self::rp($e->nominal_realisasi),
                'diff' => self::rp($e->selisih), 'by' => $e->submittedBy->name ?? '-',
                'status' => $statusLabel[$e->status] ?? $e->status,
            ])->all(),
            'summary' => [
                ['label' => 'Total Laporan', 'value' => $rows->count()],
                ['label' => 'Total Realisasi', 'value' => self::rp($rows->sum('nominal_realisasi'))],
                ['label' => 'Terverifikasi', 'value' => $rows->where('status', 'verified')->count()],
            ],
        ];
    }
}
