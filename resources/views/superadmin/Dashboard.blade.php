@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')

<div class="mb-6">
    <h1 class="text-xl font-bold text-gray-900">Dashboard</h1>
    <p class="text-sm text-gray-500">Selamat datang, {{ auth()->user()->name }}.
        {{ now()->isoFormat('dddd, D MMMM Y') }}
    </p>
</div>

{{-- Stats Utama --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="stat-card">
        <div class="stat-icon bg-blue-50 text-blue-600">📦</div>
        <div>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_products']) }}</p>
            <p class="text-sm text-gray-500">Produk Aktif</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-indigo-50 text-indigo-600">🏭</div>
        <div>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_warehouses']) }}</p>
            <p class="text-sm text-gray-500">Gudang Aktif</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-purple-50 text-purple-600">🤝</div>
        <div>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_suppliers']) }}</p>
            <p class="text-sm text-gray-500">Supplier Aktif</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-green-50 text-green-600">👥</div>
        <div>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_users']) }}</p>
            <p class="text-sm text-gray-500">User Aktif</p>
        </div>
    </div>
</div>

{{-- Keuangan bulan ini --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="stat-card bg-blue-600 text-white rounded-xl">
        <div class="stat-icon bg-white/20 text-white">🛒</div>
        <div>
            <p class="text-2xl font-bold">Rp {{ number_format($monthlyFinance['total_po'] / 1000000, 1) }}M</p>
            <p class="text-sm opacity-80">Total PO Bulan Ini</p>
        </div>
    </div>
    <div class="stat-card bg-green-600 text-white rounded-xl">
        <div class="stat-icon bg-white/20 text-white">💰</div>
        <div>
            <p class="text-2xl font-bold">Rp {{ number_format($monthlyFinance['total_so'] / 1000000, 1) }}M</p>
            <p class="text-sm opacity-80">Total SO Bulan Ini</p>
        </div>
    </div>
    <div class="stat-card bg-yellow-500 text-white rounded-xl">
        <div class="stat-icon bg-white/20 text-white">📋</div>
        <div>
            <p class="text-2xl font-bold">Rp {{ number_format($monthlyFinance['pending_budget'] / 1000000, 1) }}M</p>
            <p class="text-sm opacity-80">Anggaran Menunggu</p>
        </div>
    </div>
</div>

{{-- Chart + Low Stock --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-6">
    {{-- Chart Pergerakan Stok --}}
    <div class="card">
        <div class="card-header">
            <h3 class="font-semibold text-gray-900">Pergerakan Stok 7 Hari</h3>
        </div>
        <div class="card-body">
            <canvas id="movementChart" height="200"></canvas>
        </div>
    </div>

    {{-- Nilai Stok per Gudang --}}
    <div class="card">
        <div class="card-header">
            <h3 class="font-semibold text-gray-900">Nilai Stok per Gudang</h3>
        </div>
        <div class="card-body">
            <canvas id="warehouseChart" height="200"></canvas>
        </div>
    </div>
</div>

{{-- Tabel-tabel alert --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-6">

    {{-- Stok Menipis --}}
    <div class="card">
        <div class="card-header">
            <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse inline-block"></span>
                Stok Menipis ({{ $lowStocks->count() }})
            </h3>
            <a href="{{ route('superadmin.stocks.low-stock') }}" class="text-xs text-primary-700 hover:underline">Lihat
                semua
                →</a>
        </div>
        <div class="divide-y divide-gray-100 max-h-72 overflow-y-auto">
            @forelse($lowStocks as $stock)
            <div class="px-4 py-3 flex items-center justify-between gap-3 hover:bg-gray-50">
                <div class="min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ $stock->product->name }}</p>
                    <p class="text-xs text-gray-400">{{ $stock->warehouse->name }} · {{ $stock->product->unit }}
                    </p>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="text-sm font-bold text-red-600">{{ $stock->quantity }}</p>
                    <p class="text-xs text-gray-400">min: {{ $stock->product->min_stock }}</p>
                </div>
            </div>
            @empty
            <div class="px-4 py-8 text-center text-sm text-gray-400">Semua stok normal ✓</div>
            @endforelse
        </div>
    </div>

    {{-- PO Pending --}}
    <div class="card">
        <div class="card-header">
            <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-yellow-500 animate-pulse inline-block"></span>
                PO Menunggu Persetujuan ({{ $pendingPOs->count() }})
            </h3>
            <a href="{{ route('superadmin.purchase-orders.index', ['status' => 'pending']) }}"
                class="text-xs text-primary-700 hover:underline">Lihat semua →</a>
        </div>
        <div class="divide-y divide-gray-100 max-h-72 overflow-y-auto">
            @forelse($pendingPOs as $po)
            <div class="px-4 py-3 flex items-center justify-between gap-3 hover:bg-gray-50">
                <div class="min-w-0">
                    <a href="{{ route('superadmin.purchase-orders.show', $po) }}"
                        class="text-sm font-mono font-medium text-primary-700 hover:underline">{{ $po->po_number }}</a>
                    <p class="text-xs text-gray-400">{{ $po->supplier->name }} → {{ $po->warehouse->name }}</p>
                </div>
                <p class="text-sm font-semibold flex-shrink-0">Rp {{ number_format($po->total_amount) }}</p>
            </div>
            @empty
            <div class="px-4 py-8 text-center text-sm text-gray-400">Tidak ada PO pending</div>
            @endforelse
        </div>
    </div>

    {{-- Pengajuan Anggaran Pending --}}
    <div class="card">
        <div class="card-header">
            <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-orange-500 animate-pulse inline-block"></span>
                Anggaran Menunggu ({{ $pendingBudgets->count() }})
            </h3>
            <a href="{{ route('superadmin.budget-requests.index', ['status' => 'pending']) }}"
                class="text-xs text-primary-700 hover:underline">Lihat semua →</a>
        </div>
        <div class="divide-y divide-gray-100 max-h-72 overflow-y-auto">
            @forelse($pendingBudgets as $br)
            <div class="px-4 py-3 flex items-center justify-between gap-3 hover:bg-gray-50">
                <div class="min-w-0">
                    <a href="{{ route('superadmin.budget-requests.show', $br) }}"
                        class="text-xs font-mono text-primary-700 hover:underline">{{ $br->nomor_form }}</a>
                    <p class="text-sm font-medium text-gray-900 truncate">{{ $br->nama_item }}</p>
                    <p class="text-xs text-gray-400">{{ $br->user->name }} · {{ $br->divisi }}</p>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="text-sm font-semibold">Rp {{ number_format($br->estimasi_biaya) }}</p>
                    @if ($br->urgensi === 'mendesak')
                    <span class="badge badge-danger text-xs">Mendesak</span>
                    @endif
                </div>
            </div>
            @empty
            <div class="px-4 py-8 text-center text-sm text-gray-400">Tidak ada anggaran pending</div>
            @endforelse
        </div>
    </div>

    {{-- Transfer & Opname --}}
    <div class="card">
        <div class="card-header">
            <h3 class="font-semibold text-gray-900">Transfer & Opname Aktif</h3>
        </div>
        <div class="divide-y divide-gray-100 max-h-72 overflow-y-auto">
            @forelse($activeTransfers->merge($pendingOpnames) as $item)
            <div class="px-4 py-3 flex items-center justify-between gap-3 hover:bg-gray-50">
                @if ($item instanceof \App\Models\StockTransfer)
                <div class="min-w-0">
                    <a href="{{ route('superadmin.stock-transfers.show', $item) }}"
                        class="text-xs font-mono text-primary-700 hover:underline">{{ $item->transfer_number }}</a>
                    <p class="text-xs text-gray-500">{{ $item->fromWarehouse->name }} →
                        {{ $item->toWarehouse->name }}</p>
                </div>
                <x-status-badge :status="$item->status" />
                @else
                <div class="min-w-0">
                    <a href="{{ route('superadmin.stock-opnames.show', $item) }}"
                        class="text-xs font-mono text-primary-700 hover:underline">{{ $item->opname_number }}</a>
                    <p class="text-xs text-gray-500">{{ $item->warehouse->name }}</p>
                </div>
                <x-status-badge :status="$item->status" />
                @endif
            </div>
            @empty
            <div class="px-4 py-8 text-center text-sm text-gray-400">Tidak ada aktivitas aktif</div>
            @endforelse
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const movementData = @json($movementChart);
const labels = movementData.map(d => {
    const date = new Date(d.date);
    return date.toLocaleDateString('id-ID', {
        weekday: 'short',
        day: 'numeric',
        month: 'short'
    });
});

// Pergerakan Stok Chart
new Chart(document.getElementById('movementChart'), {
    type: 'bar',
    data: {
        labels,
        datasets: [{
                label: 'Masuk',
                data: movementData.map(d => d.total_in),
                backgroundColor: 'rgba(34,197,94,0.7)',
                borderRadius: 4
            },
            {
                label: 'Keluar',
                data: movementData.map(d => d.total_out),
                backgroundColor: 'rgba(239,68,68,0.7)',
                borderRadius: 4
            },
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top'
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Nilai Stok per Gudang
const warehouseData = @json($stockValueByWarehouse);
new Chart(document.getElementById('warehouseChart'), {
    type: 'doughnut',
    data: {
        labels: warehouseData.map(w => w.name),
        datasets: [{
            data: warehouseData.map(w => w.stock_value ?? 0),
            backgroundColor: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#06B6D4'],
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'right'
            }
        }
    }
});
</script>
@endpush