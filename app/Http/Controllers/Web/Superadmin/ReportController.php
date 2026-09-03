<?php

namespace App\Http\Controllers\Web\Superadmin;

use App\Http\Controllers\Concerns\HandlesReportExport;
use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    use HandlesReportExport;

    /**
     * Pusat Laporan — daftar semua jenis laporan yang bisa di-generate.
     */
    public function index()
    {
        $reports = ReportService::available();

        return view('superadmin.reports.index', compact('reports'));
    }

    /**
     * Generate 1 jenis laporan. ?format=csv untuk unduh CSV/Excel,
     * default menampilkan halaman cetak (siap "Save as PDF").
     */
    public function show(Request $request, string $type)
    {
        if (!array_key_exists($type, ReportService::available())) {
            abort(404, 'Jenis laporan tidak dikenali.');
        }

        $filters = $request->only([
            'warehouse_id', 'status', 'type', 'jenis', 'payment_type',
            'rekomendasi', 'period_type', 'search', 'date_from', 'date_to',
        ]);

        $report = ReportService::generate($type, $filters);

        return $this->respondReport($request, $type, $report, 'superadmin', 'superadmin.reports.index');
    }

    /**
     * Data pendukung untuk form filter (dropdown gudang, dsb).
     */
    public function filterOptions()
    {
        return response()->json([
            'warehouses' => Warehouse::where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }
}
