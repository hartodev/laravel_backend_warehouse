{{-- budget_verifications/index.blade.php --}}
@extends('layouts.app')
@section('title','Verifikasi Anggaran')
@section('breadcrumb')<span class="text-gray-700 font-medium">Verifikasi Anggaran</span>@endsection

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div><h1 class="text-xl font-bold text-gray-900">Verifikasi Anggaran</h1><p class="text-sm text-gray-500">{{ $verifications->total() }} verifikasi</p></div>
    <a href="{{ route('budget-verifications.create') }}" class="btn-primary btn">+ Buat Verifikasi</a>
</div>

<div class="card mb-5">
    <form method="GET" class="card-body flex flex-wrap gap-3 items-end">
        <div class="w-40"><label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">Semua</option>
                <option value="approved" {{ request('status')==='approved'?'selected':'' }}>Approved</option>
                <option value="partial" {{ request('status')==='partial'?'selected':'' }}>Partial</option>
                <option value="rejected" {{ request('status')==='rejected'?'selected':'' }}>Rejected</option>
            </select>
        </div>
        <button type="submit" class="btn-primary btn">Filter</button>
        <a href="{{ route('budget-verifications.index') }}" class="btn-secondary btn">Reset</a>
    </form>
</div>

<div class="table-wrap">
    <table class="data-table">
        <thead><tr><th>Pengajuan Anggaran</th><th>Item</th><th class="text-right">Anggaran</th><th class="text-right">Disetujui</th><th>Diverifikasi Oleh</th><th>Tgl.</th><th>Status</th><th class="text-right">Aksi</th></tr></thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($verifications as $v)
            <tr>
                <td><span class="font-mono text-xs text-primary-700">{{ $v->budgetRequest->nomor_form ?? '—' }}</span></td>
                <td class="max-w-xs truncate">{{ $v->budgetRequest->nama_item ?? '—' }}</td>
                <td class="text-right">Rp {{ number_format($v->budgetRequest->estimasi_biaya ?? 0) }}</td>
                <td class="text-right font-semibold">Rp {{ number_format($v->jumlah_disetujui) }}</td>
                <td>{{ $v->verifiedBy->name ?? '—' }}</td>
                <td>{{ $v->verified_at ? \Carbon\Carbon::parse($v->verified_at)->isoFormat('D MMM Y') : '—' }}</td>
                <td>
                    <span class="badge {{ $v->status === 'approved' ? 'badge-success' : ($v->status === 'partial' ? 'badge-warning' : 'badge-danger') }}">
                        {{ ucfirst($v->status) }}
                    </span>
                </td>
                <td class="text-right">
                    <a href="{{ route('budget-verifications.show', $v) }}" class="btn btn-secondary btn-sm">Detail</a>
                    <a href="{{ route('budget-verifications.edit', $v) }}" class="btn btn-secondary btn-sm">Edit</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center py-12 text-gray-400">Belum ada data verifikasi anggaran</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $verifications->links() }}</div>
@endsection
