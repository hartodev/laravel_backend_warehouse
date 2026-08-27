{{-- resources/views/superadmin/requests/index.blade.php --}}
@extends('layouts.app')
@section('title','Approval Final Request Barang')
@section('breadcrumb')<span class="text-gray-700 font-medium">Approval Final</span>@endsection

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Approval Final Request Barang</h1>
        <p class="text-sm text-gray-500">{{ $requests->total() }} request menunggu approval Anda</p>
    </div>
</div>

<div class="card mb-5">
    <form method="GET" class="card-body flex flex-wrap gap-3 items-end">
        <div class="w-48"><label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="pending_superadmin"
                    {{ request('status','pending_superadmin')==='pending_superadmin'?'selected':'' }}>Menunggu Approval
                    Final</option>
                <option value="approved" {{ request('status')==='approved'?'selected':'' }}>Disetujui</option>
                <option value="rejected" {{ request('status')==='rejected'?'selected':'' }}>Ditolak</option>
                <option value="completed" {{ request('status')==='completed'?'selected':'' }}>Selesai</option>
            </select>
        </div>
        <div class="w-36"><label class="form-label">Dari</label><input type="date" name="date_from"
                value="{{ request('date_from') }}" class="form-input"></div>
        <div class="w-36"><label class="form-label">Sampai</label><input type="date" name="date_to"
                value="{{ request('date_to') }}" class="form-input"></div>
        <button type="submit" class="btn-primary btn">Filter</button>
        <a href="{{ route('superadmin.requests.index') }}" class="btn-secondary btn">Reset</a>
    </form>
</div>

<div class="table-wrap">
    <table class="data-table">
        <thead>
            <tr>
                <th>No. Request</th>
                <th>Pemohon</th>
                <th>Diverifikasi Admin</th>
                <th class="text-right">Jml. Item</th>
                <th>Tgl. Request</th>
                <th>Status</th>
                <th class="text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($requests as $req)
            <tr>
                <td class="font-mono text-xs">{{ $req->request_number }}</td>
                <td class="font-medium">{{ $req->user->name ?? '—' }}</td>
                <td>{{ $req->adminVerifiedBy->name ?? '—' }}</td>
                <td class="text-right font-semibold">{{ $req->items->count() }} item</td>
                <td>{{ $req->created_at->isoFormat('D MMM Y') }}</td>
                <td>
                    <x-status-badge :status="$req->status" />
                </td>
                <td class="text-right">
                    <div class="flex items-center justify-end gap-1">
                        <a href="{{ route('superadmin.requests.show', $req) }}"
                            class="btn btn-secondary btn-sm">Detail</a>
                        @if($req->status === 'pending_superadmin')
                        <a href="{{ route('superadmin.requests.show', $req) }}" class="btn btn-success btn-sm">Approve
                            Final</a>
                        <button onclick="document.getElementById('rej-req-{{ $req->id }}').classList.remove('hidden')"
                            class="btn btn-danger btn-sm">Tolak</button>
                        @endif
                    </div>
                    <x-confirm-modal :id="'rej-req-'.$req->id" title="Tolak Request?"
                        :message="'Request dari '.($req->user->name ?? '—').' akan ditolak.'"
                        :action="route('superadmin.requests.reject', $req)" method="POST" confirm-text="Tolak"
                        confirm-class="btn-danger">
                        <div class="mt-3"><label class="form-label">Alasan <span
                                    class="text-red-500">*</span></label><textarea name="reject_reason" rows="2"
                                required class="form-textarea"></textarea></div>
                    </x-confirm-modal>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center py-12 text-gray-400">Tidak ada request menunggu approval final</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $requests->links() }}</div>
@endsection