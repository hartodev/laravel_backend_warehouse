{{-- resources/views/superadmin/expense_report/index.blade.php --}}
@extends('layouts.app')
@section('title','Laporan Pertanggungjawaban')
@section('breadcrumb')<span class="text-gray-700 font-medium">Laporan Pertanggungjawaban</span>@endsection

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Laporan Pertanggungjawaban</h1>
        <p class="text-sm text-gray-500">{{ $reports->total() }} laporan</p>
    </div>
    <a href="{{ route('superadmin.expense-reports.create') }}" class="btn-primary btn">+ Buat Laporan</a>
</div>

<div class="card mb-5">
    <form method="GET" class="card-body flex flex-wrap gap-3 items-end">
        <div class="w-40">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">Semua</option>
                <option value="submitted" {{ request('status')==='submitted'?'selected':'' }}>Submitted</option>
                <option value="verified" {{ request('status')==='verified'?'selected':'' }}>Verified</option>
            </select>
        </div>
        <div class="w-40">
            <label class="form-label">Dari Tanggal</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-input">
        </div>
        <div class="w-40">
            <label class="form-label">Sampai Tanggal</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-input">
        </div>
        <button type="submit" class="btn-primary btn">Filter</button>
        <a href="{{ route('superadmin.expense-reports.index') }}" class="btn-secondary btn">Reset</a>
    </form>
</div>

<div class="table-wrap">
    <table class="data-table">
        <thead>
            <tr>
                <th>Pengajuan</th>
                <th>Invoice</th>
                <th>Vendor</th>
                <th>Tgl. Transaksi</th>
                <th class="text-right">Nominal Realisasi</th>
                <th class="text-right">Selisih</th>
                <th>Disubmit Oleh</th>
                <th>Status</th>
                <th class="text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($reports as $r)
            <tr>
                <td><span class="font-mono text-xs text-primary-700">{{ $r->budgetRequest->nomor_form ?? '—' }}</span>
                </td>
                <td>{{ $r->nomor_invoice ?? '—' }}</td>
                <td>{{ $r->nama_vendor ?? '—' }}</td>
                <td>{{ \Carbon\Carbon::parse($r->tanggal_transaksi)->isoFormat('D MMM Y') }}</td>
                <td class="text-right font-semibold">Rp {{ number_format($r->nominal_realisasi, 0, ',', '.') }}</td>
                <td
                    class="text-right {{ $r->selisih < 0 ? 'text-red-600' : ($r->selisih > 0 ? 'text-green-600' : 'text-gray-500') }}">
                    Rp {{ number_format($r->selisih, 0, ',', '.') }}
                </td>
                <td>{{ $r->submittedBy->name ?? '—' }}</td>
                <td>
                    <span class="badge {{ $r->status === 'verified' ? 'badge-success' : 'badge-warning' }}">
                        {{ ucfirst($r->status) }}
                    </span>
                </td>
                <td class="text-right whitespace-nowrap">
                    <a href="{{ route('superadmin.expense-reports.show', $r) }}"
                        class="btn btn-secondary btn-sm">Detail</a>
                    @if ($r->status !== 'verified')
                    <a href="{{ route('superadmin.expense-reports.edit', $r) }}"
                        class="btn btn-secondary btn-sm">Edit</a>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center py-12 text-gray-400">Belum ada laporan pertanggungjawaban</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $reports->links() }}</div>
@endsection