@extends('layouts.admin')
@section('title', 'Verifikasi RAB')
@section('content')

<div class="admin-page-head">
    <h2>Verifikasi Anggaran</h2>
</div>

@if(session('success'))
<div class="admin-alert admin-alert-success"><i class="lucide-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="admin-alert admin-alert-error"><i class="lucide-alert-circle"></i> {{ session('error') }}</div>
@endif

<form method="GET" class="admin-filter-bar">
    <select name="rekomendasi" class="admin-select" style="max-width:180px;">
        <option value="">Semua Rekomendasi</option>
        <option value="setuju" @selected(request('rekomendasi')==='setuju' )>Setuju</option>
        <option value="tunda" @selected(request('rekomendasi')==='tunda' )>Tunda</option>
        <option value="tolak" @selected(request('rekomendasi')==='tolak' )>Tolak</option>
    </select>
    <button class="btn-outline">Filter</button>
</form>

<div class="admin-card admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>RAB Terkait</th>
                <th>Finance</th>
                <th>Rekomendasi</th>
                <th>Nominal Rekomendasi</th>
                <th>Waktu Verifikasi</th>
                <th class="cell-actions">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($verifications as $verification)
            <tr>
                <td class="cell-mono">{{ $verification->budgetRequest->nomor_form ?? '-' }}</td>
                <td>{{ $verification->finance->name ?? '-' }}</td>
                <td>
                    @if($verification->rekomendasi === 'setuju')
                    <span class="admin-badge admin-badge-success">Setuju</span>
                    @elseif($verification->rekomendasi === 'tunda')
                    <span class="admin-badge admin-badge-warning">Tunda</span>
                    @else
                    <span class="admin-badge admin-badge-danger">Tolak</span>
                    @endif
                </td>
                <td class="cell-mono">Rp {{ number_format($verification->nominal_rekomendasi ?? 0, 0, ',', '.') }}</td>
                <td class="cell-muted">{{ optional($verification->verified_at)->format('d M Y H:i') ?? '-' }}</td>
                <td class="cell-actions">
                    <a href="{{ route('admin.budget-verifications.show', $verification) }}"
                        class="admin-link">Detail</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="cell-empty">Belum ada verifikasi RAB.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="admin-pagination">{{ $verifications->appends(request()->query())->links() }}</div>
@endsection