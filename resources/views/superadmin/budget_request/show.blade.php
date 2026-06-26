@extends('superadmin.layouts.app')
@section('title', $budgetRequest->nomor_form)
@section('breadcrumb')
<a href="{{ route('superadmin.budget-requests.index') }}" class="hover:text-primary-700">Pengajuan Anggaran</a>
<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
<span class="text-gray-700 font-medium">{{ $budgetRequest->nomor_form }}</span>
@endsection

@section('content')
<div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-3 flex-wrap">
            <h1 class="text-xl font-bold text-gray-900">{{ $budgetRequest->nomor_form }}</h1>
            <span class="badge {{ $budgetRequest->jenis === 'rab' ? 'badge-info' : 'badge-warning' }}">{{ strtoupper(str_replace('_',' ',$budgetRequest->jenis)) }}</span>
            <x-status-badge :status="$budgetRequest->status" />
            @if($budgetRequest->urgensi === 'mendesak')<span class="badge badge-danger">🔴 Mendesak</span>@endif
        </div>
        <p class="text-sm text-gray-500 mt-0.5">Diajukan oleh: <strong>{{ $budgetRequest->user->name ?? '—' }}</strong> · {{ \Carbon\Carbon::parse($budgetRequest->tanggal_pengajuan)->isoFormat('D MMMM Y') }}</p>
    </div>
    <div class="flex flex-wrap gap-2">
        @if($budgetRequest->status === 'draft')
            <a href="{{ route('superadmin.budget-requests.edit', $budgetRequest) }}" class="btn-secondary btn">Edit</a>
            <form method="POST" action="{{ route('superadmin.budget-requests.submit', $budgetRequest) }}" class="inline">@csrf<button class="btn-primary btn">Submit Pengajuan</button></form>
        @elseif($budgetRequest->status === 'pending')
            <form method="POST" action="{{ route('superadmin.budget-requests.approve', $budgetRequest) }}" class="inline">
                @csrf
                <button class="btn-success btn">Setujui</button>
            </form>
            <button onclick="document.getElementById('reject-modal').classList.remove('hidden')" class="btn-danger btn">Tolak</button>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    {{-- Main info --}}
    <div class="lg:col-span-2 space-y-4">
        <div class="card">
            <div class="card-header"><h3 class="font-semibold text-gray-900">Detail Anggaran</h3></div>
            <div class="card-body grid grid-cols-2 gap-x-8 gap-y-4 text-sm">
                <div><p class="text-gray-400 text-xs mb-0.5">Divisi</p><p class="font-medium">{{ $budgetRequest->divisi }}</p></div>
                <div><p class="text-gray-400 text-xs mb-0.5">Kode Akun</p><p class="font-mono">{{ $budgetRequest->kode_akun ?? '—' }}</p></div>
                <div><p class="text-gray-400 text-xs mb-0.5">Nama Akun</p><p>{{ $budgetRequest->nama_akun ?? '—' }}</p></div>
                <div><p class="text-gray-400 text-xs mb-0.5">Urgensi</p><p class="{{ $budgetRequest->urgensi === 'mendesak' ? 'text-red-600 font-semibold' : '' }}">{{ ucfirst($budgetRequest->urgensi ?? 'normal') }}</p></div>
                <div class="col-span-2"><p class="text-gray-400 text-xs mb-0.5">Nama Item / Keperluan</p><p class="font-semibold text-gray-900">{{ $budgetRequest->nama_item }}</p></div>
                <div><p class="text-gray-400 text-xs mb-0.5">Qty</p><p>{{ $budgetRequest->qty ? number_format($budgetRequest->qty,2).' '.$budgetRequest->satuan : '—' }}</p></div>
                <div><p class="text-gray-400 text-xs mb-0.5">Estimasi Biaya</p><p class="font-bold text-primary-700 text-base">Rp {{ number_format($budgetRequest->estimasi_biaya) }}</p></div>
                @if($budgetRequest->keterangan)
                <div class="col-span-2"><p class="text-gray-400 text-xs mb-0.5">Keterangan</p><p>{{ $budgetRequest->keterangan }}</p></div>
                @endif
            </div>
        </div>

        @if($budgetRequest->jenis === 'luar_rab')
        <div class="card border-yellow-200">
            <div class="card-header bg-yellow-50"><h3 class="font-semibold text-yellow-900">Informasi Luar RAB</h3></div>
            <div class="card-body space-y-3 text-sm">
                <div><p class="text-gray-400 text-xs mb-0.5">Alasan</p><p>{{ $budgetRequest->alasan_luar_rab }}</p></div>
                @if($budgetRequest->dampak_jika_tidak)<div><p class="text-gray-400 text-xs mb-0.5">Dampak Jika Tidak Disetujui</p><p>{{ $budgetRequest->dampak_jika_tidak }}</p></div>@endif
                @if($budgetRequest->sumber_dana)<div><p class="text-gray-400 text-xs mb-0.5">Sumber Dana</p><p class="capitalize">{{ str_replace('_',' ',$budgetRequest->sumber_dana) }}</p></div>@endif
            </div>
        </div>
        @endif

        {{-- Linked records --}}
        @if($budgetRequest->verification || $budgetRequest->expenseReport)
        <div class="card">
            <div class="card-header"><h3 class="font-semibold text-gray-900">Dokumen Terkait</h3></div>
            <div class="card-body space-y-2 text-sm">
                @if($budgetRequest->verification)
                <div class="flex justify-between items-center">
                    <span class="text-gray-500">Verifikasi Anggaran</span>
                    <div class="flex items-center gap-2">
                        <x-status-badge :status="$budgetRequest->verification->status" />
                        <a href="{{ route('superadmin.budget-verifications.show', $budgetRequest->verification) }}" class="text-primary-700 hover:underline text-xs">Lihat →</a>
                    </div>
                </div>
                @endif
                @if($budgetRequest->expenseReport)
                <div class="flex justify-between items-center">
                    <span class="text-gray-500">Laporan Pertanggungjawaban</span>
                    <div class="flex items-center gap-2">
                        <x-status-badge :status="$budgetRequest->expenseReport->status" />
                        <a href="{{ route('superadmin.expense-reports.show', $budgetRequest->expenseReport) }}" class="text-primary-700 hover:underline text-xs">Lihat →</a>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>

    {{-- Approval timeline --}}
    <div class="lg:col-span-1">
        <div class="card p-5">
            <p class="font-semibold text-gray-900 mb-4">Timeline Persetujuan</p>
            <div class="space-y-4">
                @php
                    $steps = [
                        ['label'=>'Draft','done'=>true,'time'=>$budgetRequest->created_at,'by'=>$budgetRequest->user?->name],
                        ['label'=>'Disubmit','done'=>in_array($budgetRequest->status,['pending','approved','ditolak']),'time'=>null,'by'=>null],
                        ['label'=>'Branch Manager','done'=>in_array($budgetRequest->status,['approved','ditolak']),'time'=>$budgetRequest->branch_manager_at,'by'=>$budgetRequest->branchManager?->name,'note'=>$budgetRequest->catatan_branch_manager],
                    ];
                @endphp
                @foreach($steps as $step)
                <div class="flex gap-3">
                    <div class="flex-shrink-0 w-6 h-6 rounded-full {{ $step['done'] ? 'bg-green-500' : 'bg-gray-200' }} flex items-center justify-center mt-0.5">
                        @if($step['done'])<svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>@endif
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-900">{{ $step['label'] }}</p>
                        @if($step['by'])<p class="text-xs text-gray-500">{{ $step['by'] }}</p>@endif
                        @if($step['time'])<p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($step['time'])->isoFormat('D MMM Y, HH:mm') }}</p>@endif
                        @if(!empty($step['note']))<p class="text-xs text-gray-500 italic mt-0.5 bg-gray-50 rounded p-1">{{ $step['note'] }}</p>@endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- Reject modal --}}
<div id="reject-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md">
        <div class="px-6 py-5 border-b"><h3 class="font-semibold">Tolak Pengajuan</h3></div>
        <form method="POST" action="{{ route('superadmin.budget-requests.reject', $budgetRequest) }}">
            @csrf
            <div class="px-6 py-4 space-y-3">
                <textarea name="reject_reason" rows="3" required class="form-textarea" placeholder="Alasan penolakan..."></textarea>
            </div>
            <div class="px-6 py-4 border-t flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('reject-modal').classList.add('hidden')" class="btn-secondary btn">Batal</button>
                <button type="submit" class="btn-danger btn">Tolak Pengajuan</button>
            </div>
        </form>
    </div>
</div>
@endsection
