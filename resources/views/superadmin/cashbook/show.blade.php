@extends('layouts.app')

@section('title', 'Detail Buku Kas')

@section('content')
<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-0 fw-bold">Detail Buku Kas</h4>
            <small class="text-muted">{{ $cashBook->no_bukti }}</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('superadmin.cash-books.edit', $cashBook) }}" class="btn btn-warning btn-sm">
                <i class="bi bi-pencil-square me-1"></i> Edit
            </a>
            <a href="{{ route('superadmin.cash-books.index') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    {{-- Alert --}}
    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row g-4">

        {{-- Info Utama --}}
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom d-flex align-items-center gap-2 py-3">
                    <i class="bi bi-journal-text text-primary"></i>
                    <span class="fw-semibold">Informasi Kas</span>
                    <span
                        class="ms-auto badge {{ $cashBook->type === 'masuk' ? 'bg-success' : 'bg-danger' }} text-uppercase">
                        {{ $cashBook->type === 'masuk' ? 'Kas Masuk' : 'Kas Keluar' }}
                    </span>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">

                        <dt class="col-sm-4 text-muted fw-normal">No. Bukti</dt>
                        <dd class="col-sm-8 fw-semibold font-monospace">{{ $cashBook->no_bukti }}</dd>

                        <dt class="col-sm-4 text-muted fw-normal">Tanggal</dt>
                        <dd class="col-sm-8">
                            {{ \Carbon\Carbon::parse($cashBook->tanggal)->translatedFormat('d F Y') }}
                        </dd>

                        <dt class="col-sm-4 text-muted fw-normal">Pihak</dt>
                        <dd class="col-sm-8">{{ $cashBook->pihak }}</dd>

                        <dt class="col-sm-4 text-muted fw-normal">Jumlah Uang</dt>
                        <dd class="col-sm-8">
                            <span
                                class="fs-5 fw-bold {{ $cashBook->type === 'masuk' ? 'text-success' : 'text-danger' }}">
                                Rp {{ number_format($cashBook->jumlah_uang, 0, ',', '.') }}
                            </span>
                        </dd>

                        <dt class="col-sm-4 text-muted fw-normal">Terbilang</dt>
                        <dd class="col-sm-8 fst-italic text-capitalize">{{ $cashBook->terbilang }}</dd>

                        <dt class="col-sm-4 text-muted fw-normal">Keterangan</dt>
                        <dd class="col-sm-8">
                            {{ $cashBook->keterangan ?? '-' }}
                        </dd>

                    </dl>
                </div>
            </div>
        </div>

        {{-- Sidebar: Meta & Payment --}}
        <div class="col-lg-4 d-flex flex-column gap-3">

            {{-- Meta --}}
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom d-flex align-items-center gap-2 py-3">
                    <i class="bi bi-info-circle text-secondary"></i>
                    <span class="fw-semibold">Meta Data</span>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">

                        <dt class="col-5 text-muted fw-normal small">Dibuat Oleh</dt>
                        <dd class="col-7 small">
                            {{ $cashBook->createdBy?->name ?? '-' }}
                        </dd>

                        <dt class="col-5 text-muted fw-normal small">Dibuat Pada</dt>
                        <dd class="col-7 small">
                            {{ $cashBook->created_at?->translatedFormat('d M Y, H:i') ?? '-' }}
                        </dd>

                        <dt class="col-5 text-muted fw-normal small">Diupdate</dt>
                        <dd class="col-7 small">
                            {{ $cashBook->updated_at?->translatedFormat('d M Y, H:i') ?? '-' }}
                        </dd>

                    </dl>
                </div>
            </div>

            {{-- Linked Payment --}}
            @if ($cashBook->payment)
            <div class="card shadow-sm border-0 border-start border-4 border-primary">
                <div class="card-header bg-white border-bottom d-flex align-items-center gap-2 py-3">
                    <i class="bi bi-receipt text-primary"></i>
                    <span class="fw-semibold">Terkait Pembayaran</span>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-5 text-muted fw-normal small">No. Pembayaran</dt>
                        <dd class="col-7 small font-monospace fw-semibold">
                            {{ $cashBook->payment->payment_number }}
                        </dd>
                    </dl>
                </div>
            </div>
            @endif

        </div>
    </div>

    {{-- Print Button --}}
    <div class="mt-4 text-end">
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-printer me-1"></i> Cetak
        </button>
    </div>

</div>
@endsection