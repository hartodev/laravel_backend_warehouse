{{-- requests/index.blade.php --}}
@extends('layouts.app')
@section('title','Request Barang')
@section('breadcrumb')<span class="text-gray-700 font-medium">Request Barang</span>@endsection

@section('content')
<div class="flex items-center justify-between mb-6">
    <div><h1 class="text-xl font-bold text-gray-900">Request Barang</h1><p class="text-sm text-gray-500">{{ $requests->total() }} request</p></div>
</div>

<div class="card mb-5">
    <form method="GET" class="card-body flex flex-wrap gap-3 items-end">
        <div class="w-40"><label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">Semua</option>
                @foreach(['pending','approved','rejected','completed'] as $s)
                <option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-36"><label class="form-label">Dari</label><input type="date" name="date_from" value="{{ request('date_from') }}" class="form-input"></div>
        <div class="w-36"><label class="form-label">Sampai</label><input type="date" name="date_to" value="{{ request('date_to') }}" class="form-input"></div>
        <button type="submit" class="btn-primary btn">Filter</button>
        <a href="{{ route('requests.index') }}" class="btn-secondary btn">Reset</a>
    </form>
</div>

<div class="table-wrap">
    <table class="data-table">
        <thead><tr><th>Pemohon</th><th>Produk</th><th>Gudang</th><th class="text-right">Qty</th><th>Tgl. Request</th><th>Status</th><th class="text-right">Aksi</th></tr></thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($requests as $req)
            <tr>
                <td class="font-medium">{{ $req->user->name ?? '—' }}</td>
                <td>{{ $req->product->name ?? '—' }}<div class="text-xs text-gray-400 font-mono">{{ $req->product->sku ?? '' }}</div></td>
                <td>{{ $req->warehouse->name ?? '—' }}</td>
                <td class="text-right font-semibold">{{ number_format($req->quantity ?? 0) }} {{ $req->product->unit ?? '' }}</td>
                <td>{{ $req->created_at->isoFormat('D MMM Y') }}</td>
                <td><x-status-badge :status="$req->status" /></td>
                <td class="text-right">
                    <div class="flex items-center justify-end gap-1">
                        <a href="{{ route('requests.show', $req) }}" class="btn btn-secondary btn-sm">Detail</a>
                        @if($req->status === 'pending')
                        <form method="POST" action="{{ route('requests.approve', $req) }}" class="inline">@csrf<button class="btn btn-success btn-sm">Setujui</button></form>
                        <button onclick="document.getElementById('rej-req-{{ $req->id }}').classList.remove('hidden')" class="btn btn-danger btn-sm">Tolak</button>
                        @elseif($req->status === 'approved')
                        <form method="POST" action="{{ route('requests.complete', $req) }}" class="inline">@csrf<button class="btn btn-primary btn-sm">Selesai</button></form>
                        @endif
                    </div>
                    <x-confirm-modal :id="'rej-req-'.$req->id" title="Tolak Request?" :message="'Request dari '.($req->user->name ?? '—').' akan ditolak.'" :action="route('requests.reject', $req)" method="POST" confirm-text="Tolak" confirm-class="btn-danger">
                        <div class="mt-3"><label class="form-label">Alasan <span class="text-red-500">*</span></label><textarea name="reject_reason" rows="2" required class="form-textarea"></textarea></div>
                    </x-confirm-modal>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center py-12 text-gray-400">Belum ada request barang</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $requests->links() }}</div>
@endsection
