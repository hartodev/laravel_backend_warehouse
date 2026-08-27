@extends('layouts.admin')
@section('title', 'Detail Pengajuan RAB')
@section('content')

<div class="admin-page-head">
    <h2>{{ $budgetRequest->nomor_form }}</h2>
    @if($budgetRequest->status === 'pending')
    <span class="admin-badge admin-badge-warning">Pending</span>
    @elseif($budgetRequest->status === 'pending_sa')
    <span class="admin-badge admin-badge-info">Menunggu Super Admin</span>
    @elseif($budgetRequest->status === 'ditunda')
    <span class="admin-badge admin-badge-warning">Ditunda</span>
    @elseif($budgetRequest->status === 'ditolak')
    <span class="admin-badge admin-badge-danger">Ditolak</span>
    @elseif(str_starts_with($budgetRequest->status ?? '', 'approved'))
    <span class="admin-badge admin-badge-success">Disetujui</span>
    @else
    <span class="admin-badge admin-badge-muted">{{ $budgetRequest->status }}</span>
    @endif
</div>

@if(session('success'))
<div class="admin-alert admin-alert-success"><i class="lucide-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="admin-alert admin-alert-error"><i class="lucide-alert-circle"></i> {{ session('error') }}</div>
@endif
@if ($errors->any())
<div class="admin-alert admin-alert-error">
    <div>
        @foreach ($errors->all() as $error)
        <div>{{ $error }}</div>
        @endforeach
    </div>
</div>
@endif

<div class="admin-detail-grid" style="margin-bottom:20px;">
    <div class="admin-detail-item">
        <p class="admin-label">Pemohon</p>
        <p>{{ $budgetRequest->user->name ?? '-' }} <span
                class="cell-muted">({{ $budgetRequest->user->email ?? '-' }})</span></p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">Divisi</p>
        <p>{{ $budgetRequest->divisi ?? '-' }}</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">Jenis</p>
        <p>{{ ucfirst($budgetRequest->jenis ?? '-') }}</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">Urgensi</p>
        <p>{{ ucfirst($budgetRequest->urgensi ?? '-') }}</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">Tanggal Pengajuan</p>
        <p>{{ optional($budgetRequest->tanggal_pengajuan)->format('d M Y') ?? '-' }}</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">Nama Item</p>
        <p>{{ $budgetRequest->nama_item ?? '-' }}</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">Total Estimasi</p>
        <p class="cell-mono">Rp {{ number_format($budgetRequest->total_estimasi ?? 0, 0, ',', '.') }}</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">Total Realisasi</p>
        <p class="cell-mono">Rp {{ number_format($budgetRequest->total_realisasi ?? 0, 0, ',', '.') }}</p>
    </div>
</div>

<div class="admin-card admin-table-wrap" style="margin-bottom:20px;">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Nama Item</th>
                <th>Qty</th>
                <th>Harga Satuan</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($budgetRequest->items as $item)
            <tr>
                <td>{{ $item->nama_item ?? $item->name ?? '-' }}</td>
                <td class="cell-mono">{{ $item->qty ?? $item->jumlah ?? '-' }}</td>
                <td class="cell-mono">Rp {{ number_format($item->harga_satuan ?? 0, 0, ',', '.') }}</td>
                <td class="cell-mono">Rp {{ number_format($item->subtotal ?? 0, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="cell-empty">Tidak ada rincian item.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="admin-detail-grid" style="margin-bottom:20px;">
    <div class="admin-detail-item">
        <p class="admin-label">Disetujui Admin</p>
        <p>{{ $budgetRequest->adminApprover->name ?? '-' }} @if($budgetRequest->branch_manager_at) ·
            {{ $budgetRequest->branch_manager_at->format('d M Y H:i') }} @endif</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">Disetujui Super Admin</p>
        <p>{{ $budgetRequest->superAdminApprover->name ?? '-' }}</p>
    </div>
    <div class="admin-detail-item" style="grid-column:span 2;">
        <p class="admin-label">Catatan Admin</p>
        <p>{{ $budgetRequest->catatan_branch_manager ?: '-' }}</p>
    </div>
</div>

@if($budgetRequest->status === 'pending')
<div class="admin-card admin-card-pad" style="margin-bottom:20px;">
    <h3 style="margin-top:0;">Review Pengajuan</h3>
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <form action="{{ route('admin.budget-requests.approve', $budgetRequest) }}" method="POST"
            style="display:inline;" onsubmit="return confirm('Setujui pengajuan RAB ini?');">
            @csrf
            <button type="submit" class="btn-primary ripple">Setujui</button>
        </form>

        <button type="button" class="btn-outline"
            onclick="document.getElementById('tunda-form').style.display='flex'">Tunda</button>
        <button type="button" class="btn-danger"
            onclick="document.getElementById('reject-form').style.display='flex'">Tolak</button>
    </div>

    <form id="tunda-form" action="{{ route('admin.budget-requests.tunda', $budgetRequest) }}" method="POST"
        style="display:none;gap:10px;margin-top:14px;align-items:flex-end;">
        @csrf
        <div style="flex:1;">
            <label class="admin-label">Catatan Penundaan</label>
            <textarea name="catatan" required class="admin-textarea"></textarea>
        </div>
        <button type="submit" class="btn-outline">Kirim</button>
    </form>

    <form id="reject-form" action="{{ route('admin.budget-requests.reject', $budgetRequest) }}" method="POST"
        style="display:none;gap:10px;margin-top:14px;align-items:flex-end;">
        @csrf
        <div style="flex:1;">
            <label class="admin-label">Alasan Penolakan</label>
            <textarea name="catatan" required class="admin-textarea"></textarea>
        </div>
        <button type="submit" class="btn-danger">Tolak</button>
    </form>
</div>
@endif

<div class="admin-action-panel">
    <a href="{{ route('admin.budget-requests.index') }}" class="btn-secondary">← Kembali</a>
</div>
@endsection