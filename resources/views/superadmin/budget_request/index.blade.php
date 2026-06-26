{{-- budget_requests/index.blade.php --}}
@extends('layouts.app')
@section('title','Pengajuan Anggaran')
@section('breadcrumb')<span class="text-gray-700 font-medium">Pengajuan Anggaran</span>@endsection

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Pengajuan Anggaran</h1>
        <p class="text-sm text-gray-500 mt-0.5">{{ $brs->total() }} pengajuan</p>
    </div>
    <a href="{{ route('budget-requests.create') }}" class="btn-primary btn">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Buat Pengajuan
    </a>
</div>

<div class="card mb-5">
    <form method="GET" class="card-body flex flex-wrap gap-3 items-end">
        <div class="w-36">
            <label class="form-label">Jenis</label>
            <select name="jenis" class="form-select">
                <option value="">Semua</option>
                <option value="rab" {{ request('jenis')==='rab'?'selected':'' }}>RAB</option>
                <option value="luar_rab" {{ request('jenis')==='luar_rab'?'selected':'' }}>Luar RAB</option>
            </select>
        </div>
        <div class="w-40">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">Semua</option>
                @foreach(['draft','pending','approved','ditolak'] as $s)
                <option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex-1 min-w-40">
            <label class="form-label">Divisi</label>
            <input type="text" name="divisi" value="{{ request('divisi') }}" placeholder="Cari divisi..." class="form-input">
        </div>
        <div class="w-36">
            <label class="form-label">Dari</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-input">
        </div>
        <div class="w-36">
            <label class="form-label">Sampai</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-input">
        </div>
        <button type="submit" class="btn-primary btn">Filter</button>
        <a href="{{ route('budget-requests.index') }}" class="btn-secondary btn">Reset</a>
    </form>
</div>

<div class="table-wrap">
    <table class="data-table">
        <thead>
            <tr>
                <th>No. Form</th>
                <th>Divisi</th>
                <th>Nama Item</th>
                <th>Jenis</th>
                <th>Urgensi</th>
                <th class="text-right">Estimasi Biaya</th>
                <th>Tgl. Pengajuan</th>
                <th>Status</th>
                <th class="text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($brs as $br)
            <tr>
                <td><span class="font-mono text-xs font-medium text-primary-700">{{ $br->nomor_form }}</span></td>
                <td>{{ $br->divisi }}</td>
                <td class="max-w-xs truncate font-medium">{{ $br->nama_item }}</td>
                <td>
                    <span class="badge {{ $br->jenis === 'rab' ? 'badge-info' : 'badge-warning' }}">
                        {{ strtoupper(str_replace('_',' ',$br->jenis)) }}
                    </span>
                </td>
                <td>
                    <span class="badge {{ $br->urgensi === 'mendesak' ? 'badge-danger' : 'badge-gray' }}">
                        {{ ucfirst($br->urgensi ?? 'normal') }}
                    </span>
                </td>
                <td class="text-right font-semibold">Rp {{ number_format($br->estimasi_biaya) }}</td>
                <td>{{ \Carbon\Carbon::parse($br->tanggal_pengajuan)->isoFormat('D MMM Y') }}</td>
                <td><x-status-badge :status="$br->status" /></td>
                <td class="text-right">
                    <div class="flex items-center justify-end gap-1">
                        <a href="{{ route('budget-requests.show', $br) }}" class="btn btn-secondary btn-sm">Detail</a>
                        @if($br->status === 'draft')
                            <form method="POST" action="{{ route('budget-requests.submit', $br) }}" class="inline">@csrf<button class="btn btn-primary btn-sm">Submit</button></form>
                        @elseif($br->status === 'pending')
                            <form method="POST" action="{{ route('budget-requests.approve', $br) }}" class="inline">@csrf<button class="btn btn-success btn-sm">Setujui</button></form>
                            <button onclick="document.getElementById('reject-br-{{ $br->id }}').classList.remove('hidden')" class="btn btn-danger btn-sm">Tolak</button>
                        @endif
                    </div>
                    <x-confirm-modal :id="'reject-br-'.$br->id" title="Tolak Pengajuan?" :message="'Pengajuan '.$br->nomor_form.' akan ditolak.'"
                        :action="route('budget-requests.reject', $br)" method="POST" confirm-label="Tolak" confirm-class="btn-danger">
                        <div class="mt-3">
                            <label class="form-label">Alasan <span class="text-red-500">*</span></label>
                            <textarea name="reject_reason" rows="2" required class="form-textarea"></textarea>
                        </div>
                    </x-confirm-modal>
                </td>
            </tr>
            @empty
            <tr><td colspan="9" class="text-center py-12 text-gray-400">Belum ada pengajuan anggaran</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $brs->links() }}</div>
@endsection
