@extends('layouts.admin')
@section('title', 'Pusat Laporan')
@section('content')

<div class="admin-page-head">
    <h2>Pusat Laporan</h2>
    <p class="cell-muted" style="margin-top:4px;">Generate &amp; unduh laporan untuk seluruh modul gudang, stok,
        transaksi, dan keuangan.</p>
</div>

<div class="row g-3">
    @foreach($reports as $key => $label)
    <div class="col-12 col-sm-6 col-lg-4">
        <div class="admin-card" style="padding:16px; height:100%; display:flex; flex-direction:column; justify-content:space-between;">
            <div>
                <h3 style="font-size:1rem; font-weight:600; margin-bottom:4px;">{{ $label }}</h3>
                <p class="cell-muted" style="font-size:.85rem;">Lihat &amp; generate laporan {{ strtolower($label) }}.</p>
            </div>
            <div class="d-flex gap-2 mt-2">
                <a href="{{ route('admin.reports.show', $key) }}" class="btn-outline btn-sm">Lihat / Cetak</a>
                <a href="{{ route('admin.reports.show', $key) }}?format=csv" class="btn-outline btn-sm">Unduh Excel (CSV)</a>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
