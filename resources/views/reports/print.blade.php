<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} · GudangPro</title>
    <style>
        :root {
            --primary: #2563eb;
            --border: #e2e8f0;
            --muted: #64748b;
            --bg: #f8fafc;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            padding: 24px;
            color: #1e293b;
            background: var(--bg);
        }

        .toolbar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 16px;
        }

        .toolbar a, .toolbar button {
            display: inline-block;
            font-size: 13px;
            padding: 8px 14px;
            border-radius: 6px;
            border: 1px solid var(--border);
            background: #fff;
            color: #1e293b;
            text-decoration: none;
            cursor: pointer;
        }

        .toolbar .primary {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
        }

        .filter-bar {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 14px 16px;
            margin-bottom: 16px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: end;
        }

        .filter-bar .field { display: flex; flex-direction: column; gap: 4px; }
        .filter-bar label { font-size: 11px; color: var(--muted); font-weight: 600; text-transform: uppercase; }
        .filter-bar input, .filter-bar select {
            padding: 6px 8px; border: 1px solid var(--border); border-radius: 6px; font-size: 13px;
        }

        .sheet {
            background: #fff;
            border-radius: 10px;
            border: 1px solid var(--border);
            padding: 28px 32px;
        }

        .sheet-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #1e293b;
            padding-bottom: 14px;
            margin-bottom: 18px;
        }

        .sheet-header h1 { font-size: 20px; margin: 0 0 4px; }
        .sheet-header .company { font-weight: 700; font-size: 15px; }
        .sheet-header .meta { font-size: 12px; color: var(--muted); text-align: right; }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12.5px;
        }

        thead th {
            background: #1e293b;
            color: #fff;
            text-align: left;
            padding: 8px 10px;
            white-space: nowrap;
        }

        tbody td {
            padding: 7px 10px;
            border-bottom: 1px solid var(--border);
        }

        tbody tr:nth-child(even) { background: #f8fafc; }

        .empty-row td { text-align: center; padding: 24px; color: var(--muted); }

        .summary {
            margin-top: 18px;
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .summary .box {
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 10px 16px;
            min-width: 160px;
        }

        .summary .box .label { font-size: 11px; color: var(--muted); text-transform: uppercase; font-weight: 600; }
        .summary .box .value { font-size: 16px; font-weight: 700; margin-top: 2px; }

        .footer-note {
            margin-top: 24px;
            font-size: 11px;
            color: var(--muted);
            display: flex;
            justify-content: space-between;
        }

        @media print {
            body { background: #fff; padding: 0; }
            .toolbar, .filter-bar { display: none !important; }
            .sheet { border: none; padding: 0; }
        }
    </style>
</head>

<body>
    <div class="toolbar">
        <a href="{{ route($backRoute) }}">&larr; Kembali ke Pusat Laporan</a>
        <div style="display:flex; gap:8px;">
            <a href="{{ route($showRoute, $type) }}?{{ http_build_query(array_merge(request()->query(), ['format' => 'csv'])) }}">
                Unduh Excel (CSV)
            </a>
            <button class="primary" onclick="window.print()">Cetak / Simpan sebagai PDF</button>
        </div>
    </div>

    <form method="GET" class="filter-bar">
        <input type="hidden" name="format" value="view">
        @if(in_array($type, ['stocks','stock-movements','stock-transfers','stock-opnames','stock-report','purchase-orders','sales-orders']))
        <div class="field">
            <label>Gudang</label>
            <select name="warehouse_id">
                <option value="">Semua Gudang</option>
                @foreach($warehouses as $w)
                <option value="{{ $w->id }}" @selected(($filters['warehouse_id'] ?? null) == $w->id)>{{ $w->name }}</option>
                @endforeach
            </select>
        </div>
        @endif

        @if(in_array($type, ['stock-movements','stock-transfers','stock-opnames','stock-report','product-submissions','purchase-orders','sales-orders','payments','cashbook','budget-requests','budget-verifications','budget-revisions','expense-reports']))
        <div class="field">
            <label>Dari Tanggal</label>
            <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}">
        </div>
        <div class="field">
            <label>Sampai Tanggal</label>
            <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}">
        </div>
        @endif

        @if($type === 'stocks' || $type === 'warehouses')
        <div class="field">
            <label>Cari</label>
            <input type="text" name="search" placeholder="Nama / SKU / kode..." value="{{ $filters['search'] ?? '' }}">
        </div>
        @endif

        <div class="field">
            <label>&nbsp;</label>
            <button type="submit" class="primary" style="border:none; cursor:pointer;">Terapkan Filter</button>
        </div>
        <div class="field">
            <label>&nbsp;</label>
            <a href="{{ route($showRoute, $type) }}">Reset</a>
        </div>
    </form>

    <div class="sheet">
        <div class="sheet-header">
            <div>
                <div class="company">GudangPro</div>
                <h1>{{ $title }}</h1>
            </div>
            <div class="meta">
                Dicetak oleh: {{ $generatedBy }}<br>
                Tanggal cetak: {{ $generatedAt }}<br>
                Total data: {{ count($rows) }}
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    @foreach($columns as $label => $key)
                    <th>{{ $label }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                <tr>
                    @foreach($columns as $label => $key)
                    <td>{{ $row[$key] ?? '-' }}</td>
                    @endforeach
                </tr>
                @empty
                <tr class="empty-row">
                    <td colspan="{{ count($columns) }}">Tidak ada data untuk filter yang dipilih.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if(count($summary))
        <div class="summary">
            @foreach($summary as $s)
            <div class="box">
                <div class="label">{{ $s['label'] }}</div>
                <div class="value">{{ $s['value'] }}</div>
            </div>
            @endforeach
        </div>
        @endif

        <div class="footer-note">
            <span>Dokumen ini digenerate otomatis oleh sistem GudangPro.</span>
            <span>{{ $generatedAt }}</span>
        </div>
    </div>
</body>

</html>
