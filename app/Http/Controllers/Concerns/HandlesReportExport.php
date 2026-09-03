<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * HandlesReportExport
 * --------------------
 * Dipakai oleh Admin\ReportController & Superadmin\ReportController supaya
 * setiap laporan bisa langsung:
 *   - Ditampilkan sebagai halaman cetak rapi (siap "Save as PDF" dari browser)
 *   - Diunduh sebagai file CSV (bisa dibuka langsung di Excel)
 * tanpa perlu install package tambahan (dompdf / maatwebsite/excel).
 */
trait HandlesReportExport
{
    /**
     * @param  string  $panel  'admin' atau 'superadmin' — dipakai untuk judul & link kembali
     */
    protected function respondReport(Request $request, string $type, array $report, string $panel, string $backRoute)
    {
        $format = $request->query('format', 'view');

        if ($format === 'csv') {
            return $this->streamCsv($type, $report);
        }

        return view('reports.print', [
            'type' => $type,
            'title' => $report['title'],
            'columns' => $report['columns'],
            'rows' => $report['rows'],
            'summary' => $report['summary'] ?? [],
            'panel' => $panel,
            'backRoute' => $backRoute,
            'showRoute' => $panel . '.reports.show',
            'warehouses' => \App\Models\Warehouse::orderBy('name')->get(['id', 'name']),
            'generatedAt' => now()->format('d/m/Y H:i'),
            'generatedBy' => auth()->user()->name ?? 'Sistem',
            'filters' => $request->only(['warehouse_id', 'status', 'type', 'jenis', 'payment_type', 'rekomendasi', 'period_type', 'search', 'date_from', 'date_to']),
        ]);
    }

    protected function streamCsv(string $type, array $report): StreamedResponse
    {
        $filename = $type . '-' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($report) {
            $out = fopen('php://output', 'w');
            // BOM supaya karakter dibaca benar saat dibuka di Excel
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, array_keys($report['columns']));

            foreach ($report['rows'] as $row) {
                $line = [];
                foreach ($report['columns'] as $key) {
                    $line[] = $row[$key] ?? '';
                }
                fputcsv($out, $line);
            }

            if (!empty($report['summary'])) {
                fputcsv($out, []);
                fputcsv($out, ['Ringkasan']);
                foreach ($report['summary'] as $s) {
                    fputcsv($out, [$s['label'], $s['value']]);
                }
            }

            fclose($out);
        }, 200, $headers);
    }
}
