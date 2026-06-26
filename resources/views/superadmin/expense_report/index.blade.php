{{-- ============================================================
     expense_reports/index.blade.php
============================================================ --}}
@extends('layouts.app')
@section('title', 'Laporan Pertanggungjawaban')
@section('breadcrumb')<span class="text-gray-700 font-medium">Lap. Pertanggungjawaban</span>@endsection

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Laporan Pertanggungjawaban</h1>
            <p class="text-sm text-gray-500">{{ $reports->total() }} laporan</p>
        </div>
        <a href="{{ route('expense-reports.create') }}" class="btn-primary btn">+ Buat Laporan</a>
    </div>

    <div class="card mb-5">
        <form method="GET" class="card-body flex flex-wrap gap-3 items-end">
            <div class="w-40"><label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua</option>
                    <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Submitted</option>
                    <option value="verified" {{ request('status') === 'verified' ? 'selected' : '' }}>Verified</option>
                </select>
            </div>
            <div class="w-36"><label class="form-label">Dari</label><input type="date" name="date_from"
                    value="{{ request('date_from') }}" class="form-input"></div>
            <div class="w-36"><label class="form-label">Sampai</label><input type="date" name="date_to"
                    value="{{ request('date_to') }}" class="form-input"></div>
            <button type="submit" class="btn-primary btn">Filter</button>
            <a href="{{ route('expense-reports.index') }}" class="btn-secondary btn">Reset</a>
        </form>
    </div>

    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Pengajuan Anggaran</th>
                    <th>Vendor</th>
                    <th>Tgl. Transaksi</th>
                    <th class="text-right">Anggaran</th>
                    <th class="text-right">Realisasi</th>
                    <th class="text-right">Selisih</th>
                    <th>Status</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($reports as $r)
                    <tr>
                        <td><span
                                class="text-xs font-mono text-primary-700">{{ $r->budgetRequest->nomor_form ?? '—' }}</span>
                            <div class="text-xs text-gray-500 truncate max-w-40">{{ $r->budgetRequest->nama_item ?? '' }}
                            </div>
                        </td>
                        <td>{{ $r->nama_vendor ?? '—' }}</td>
                        <td>{{ \Carbon\Carbon::parse($r->tanggal_transaksi)->isoFormat('D MMM Y') }}</td>
                        <td class="text-right">Rp {{ number_format($r->budgetRequest->estimasi_biaya ?? 0) }}</td>
                        <td class="text-right font-semibold">Rp {{ number_format($r->nominal_realisasi) }}</td>
                        <td
                            class="text-right font-semibold {{ ($r->selisih ?? 0) > 0 ? 'text-red-600' : 'text-green-700' }}">
                            {{ ($r->selisih ?? 0) > 0 ? '+' : '' }}Rp {{ number_format($r->selisih ?? 0) }}
                        </td>
                        <td><x-status-badge :status="$r->status" /></td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('expense-reports.show', $r) }}"
                                    class="btn btn-secondary btn-sm">Detail</a>
                                @if ($r->status === 'submitted')
                                    <form method="POST" action="{{ route('superadmin.expense-reports.verify', $r) }}"
                                        class="inline">@csrf<button class="btn btn-success btn-sm">Verifikasi</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-12 text-gray-400">Belum ada laporan pertanggungjawaban</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $reports->links() }}</div>
@endsection
