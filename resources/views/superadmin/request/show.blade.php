{{-- resources/views/superadmin/request/show.blade.php --}}
{{-- PENTING: pastikan file ini fisiknya ada di folder "request" (singular),
     karena controller manggil view('superadmin.request.show') --}}
@extends('layouts.app')
@section('title', 'Detail Request')
@section('breadcrumb')
    <a href="{{ route('requests.index') }}" class="text-gray-500 hover:text-gray-700">Approval Final</a>
    <span class="mx-1 text-gray-300">/</span>
    <span class="text-gray-700 font-medium">{{ $request->request_number }}</span>
@endsection

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Request #{{ $request->request_number }}</h1>
            <p class="text-sm text-gray-500">
                Diajukan oleh {{ $request->user->name ?? '—' }} ·
                {{ $request->created_at?->isoFormat('D MMM Y, HH:mm') ?? 'Tanggal tidak tersedia' }}
            </p>
        </div>
        <x-status-badge :status="$request->status" />
    </div>

    @if ($request->note)
        <div class="card mb-5">
            <div class="card-body">
                <div class="text-xs font-semibold text-gray-500 uppercase mb-1">Catatan Pemohon</div>
                <p class="text-sm text-gray-700">{{ $request->note }}</p>
            </div>
        </div>
    @endif

    @if ($request->admin_note)
        <div class="card mb-5">
            <div class="card-body">
                <div class="text-xs font-semibold text-gray-500 uppercase mb-1">
                    Catatan Verifikasi Admin ({{ $request->adminVerifiedBy->name ?? '—' }})
                </div>
                <p class="text-sm text-gray-700">{{ $request->admin_note }}</p>
            </div>
        </div>
    @endif

    {{-- Info approve/reject final — relasi approvedBy sudah di-eager-load di controller,
         tapi sebelumnya belum ditampilkan sama sekali di view --}}
    @if (in_array($request->status, ['approved', 'rejected']) && $request->approvedBy)
        <div class="card mb-5">
            <div class="card-body">
                <div class="text-xs font-semibold text-gray-500 uppercase mb-1">
                    {{ $request->status === 'approved' ? 'Disetujui Final Oleh' : 'Ditolak Oleh' }}
                </div>
                <p class="text-sm text-gray-700">
                    {{ $request->approvedBy->name ?? '—' }} ·
                    {{ $request->approved_at?->isoFormat('D MMM Y, HH:mm') ?? '—' }}
                </p>
            </div>
        </div>
    @endif

    @if ($request->status === 'pending_superadmin')
        <form method="POST" action="{{ route('requests.approveFinal', $request) }}">
            @csrf
            <div class="card mb-5">
                <div class="card-body">
                    <div class="w-64 mb-4">
                        <label class="form-label">Gudang Sumber <span class="text-red-500">*</span></label>
                        <select name="warehouse_id" required class="form-select">
                            <option value="">-- Pilih Gudang --</option>
                            @foreach ($warehouses as $w)
                                <option value="{{ $w->id }}">{{ $w->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th class="text-right">Diminta</th>
                                <th class="text-right">Diverifikasi Admin</th>
                                <th class="text-right w-32">Jumlah Final</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($request->items as $item)
                                <tr>
                                    <td>
                                        {{ $item->product->name ?? 'Produk tidak ditemukan' }}
                                        <div class="text-xs text-gray-400 font-mono">
                                            {{ $item->product->sku ?? '' }}
                                        </div>
                                    </td>
                                    <td class="text-right">{{ $item->quantity }} {{ $item->product->unit ?? '' }}</td>
                                    <td class="text-right">{{ $item->approved_quantity ?? '—' }}</td>
                                    <td class="text-right">
                                        <input type="hidden" name="items[{{ $loop->index }}][request_item_id]"
                                            value="{{ $item->id }}">
                                        <input type="number" min="0"
                                            name="items[{{ $loop->index }}][approved_quantity]"
                                            value="{{ $item->approved_quantity ?? $item->quantity }}"
                                            class="form-input text-right w-28">
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-sm text-gray-400 py-4">
                                        Tidak ada item pada request ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="btn btn-success">Konfirmasi Approve Final</button>
                <button type="button"
                    onclick="document.getElementById('rej-req-{{ $request->id }}').classList.remove('hidden')"
                    class="btn btn-danger">Tolak</button>
            </div>
        </form>

        <x-confirm-modal :id="'rej-req-' . $request->id" title="Tolak Request?" :message="'Request dari ' . ($request->user->name ?? '—') . ' akan ditolak.'" :action="route('requests.reject', $request)" method="POST"
            confirm-text="Tolak" confirm-class="btn-danger">
            <div class="mt-3">
                <label class="form-label">Alasan <span class="text-red-500">*</span></label>
                <textarea name="reject_reason" rows="2" required class="form-textarea"></textarea>
            </div>
        </x-confirm-modal>
    @else
        <div class="card">
            <div class="card-body">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th class="text-right">Diminta</th>
                            <th class="text-right">Disetujui</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($request->items as $item)
                            <tr>
                                <td>{{ $item->product->name ?? 'Produk tidak ditemukan' }}</td>
                                <td class="text-right">{{ $item->quantity }} {{ $item->product->unit ?? '' }}</td>
                                <td class="text-right">{{ $item->approved_quantity ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-sm text-gray-400 py-4">
                                    Tidak ada item pada request ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                @if ($request->status === 'rejected' && $request->reject_reason)
                    <div class="mt-4 text-sm text-red-600">Alasan penolakan: {{ $request->reject_reason }}</div>
                @endif
            </div>
        </div>
    @endif
@endsection




