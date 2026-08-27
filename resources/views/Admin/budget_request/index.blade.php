@extends('layouts.admin')
@section('title', 'Pengajuan RAB')
@section('content')

<div class="admin-page-head">
    <h2>Pengajuan RAB</h2>
</div>

@if(session('success'))
<div class="admin-alert admin-alert-success"><i class="lucide-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="admin-alert admin-alert-error"><i class="lucide-alert-circle"></i> {{ session('error') }}</div>
@endif

<form method="GET" class="admin-filter-bar">
    <input type="text" name="divisi" value="{{ request('divisi') }}" placeholder="Cari divisi..." class="admin-input"
        style="max-width:200px;">
    <select name="jenis" class="admin-select" style="max-width:160px;">
        <option value="">Semua Jenis</option>
        <option value="barang" @selected(request('jenis')==='barang' )>Barang</option>
        <option value="jasa" @selected(request('jenis')==='jasa' )>Jasa</option>
    </select>
    <select name="urgensi" class="admin-select" style="max-width:160px;">
        <option value="">Semua Urgensi</option>
        <option value="rendah" @selected(request('urgensi')==='rendah' )>Rendah</option>
        <option value="sedang" @selected(request('urgensi')==='sedang' )>Sedang</option>
        <option value="tinggi" @selected(request('urgensi')==='tinggi' )>Tinggi</option>
    </select>
    <select name="status" class="admin-select" style="max-width:180px;">
        <option value="">Semua Status</option>
        <option value="pending" @selected(request('status')==='pending' )>Pending</option>
        <option value="pending_sa" @selected(request('status')==='pending_sa' )>Menunggu Super Admin</option>
        <option value="ditunda" @selected(request('status')==='ditunda' )>Ditunda</option>
        <option value="ditolak" @selected(request('status')==='ditolak' )>Ditolak</option>
        <option value="approved" @selected(request('status')==='approved' )>Disetujui</option>
    </select>
    <input type="date" name="date_from" value="{{ request('date_from') }}" class="admin-input" style="max-width:160px;">
    <input type="date" name="date_to" value="{{ request('date_to') }}" class="admin-input" style="max-width:160px;">
    <button class="btn-outline">Filter</button>
</form>

<div class="admin-card admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>No. Form</th>
                <th>Pemohon</th>
                <th>Divisi</th>
                <th>Jenis</th>
                <th>Urgensi</th>
                <th>Total Estimasi</th>
                <th>Status</th>
                <th class="cell-actions">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($brs as $br)
            <tr>
                <td class="cell-mono">{{ $br->nomor_form }}</td>
                <td>{{ $br->user->name ?? '-' }}</td>
                <td class="cell-muted">{{ $br->divisi ?? '-' }}</td>
                <td class="cell-muted">{{ ucfirst($br->jenis ?? '-') }}</td>
                <td>
                    @if($br->urgensi === 'tinggi')
                    <span class="admin-badge admin-badge-danger">Tinggi</span>
                    @elseif($br->urgensi === 'sedang')
                    <span class="admin-badge admin-badge-warning">Sedang</span>
                    @else
                    <span class="admin-badge admin-badge-muted">{{ ucfirst($br->urgensi ?? '-') }}</span>
                    @endif
                </td>
                <td class="cell-mono">Rp {{ number_format($br->total_estimasi ?? 0, 0, ',', '.') }}</td>
                <td>
                    @if($br->status === 'pending')
                    <span class="admin-badge admin-badge-warning">Pending</span>
                    @elseif($br->status === 'pending_sa')
                    <span class="admin-badge admin-badge-info">Menunggu Super Admin</span>
                    @elseif($br->status === 'ditunda')
                    <span class="admin-badge admin-badge-warning">Ditunda</span>
                    @elseif($br->status === 'ditolak')
                    <span class="admin-badge admin-badge-danger">Ditolak</span>
                    @elseif(str_starts_with($br->status ?? '', 'approved'))
                    <span class="admin-badge admin-badge-success">Disetujui</span>
                    @else
                    <span class="admin-badge admin-badge-muted">{{ $br->status }}</span>
                    @endif
                </td>
                <td class="cell-actions">
                    <a href="{{ route('admin.budget-requests.show', $br) }}" class="admin-link">Detail</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="cell-empty">Belum ada pengajuan RAB.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="admin-pagination">{{ $brs->appends(request()->query())->links() }}</div>
@endsection