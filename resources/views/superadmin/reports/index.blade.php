{{-- superadmin/reports/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Pusat Laporan')
@section('breadcrumb')<span class="text-gray-700 font-medium">Pusat Laporan</span>@endsection

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Pusat Laporan</h1>
        <p class="text-sm text-gray-500 mt-1">Generate & unduh laporan untuk seluruh modul gudang, stok, transaksi,
            dan keuangan.</p>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    @foreach($reports as $key => $label)
    <div class="card">
        <div class="card-body">
            <h3 class="font-semibold text-gray-900 mb-1">{{ $label }}</h3>
            <p class="text-sm text-gray-500 mb-4">Lihat &amp; generate laporan {{ strtolower($label) }}.</p>
            <div class="flex gap-2">
                <a href="{{ route('superadmin.reports.show', $key) }}" class="btn-primary btn text-sm">
                    Lihat / Cetak
                </a>
                <a href="{{ route('superadmin.reports.show', $key) }}?format=csv" class="btn-secondary btn text-sm">
                    Unduh Excel (CSV)
                </a>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
