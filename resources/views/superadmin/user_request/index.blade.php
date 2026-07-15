@extends('layouts.app')

@section('title', 'Pengajuan User')
@section('breadcrumb')
    <span class="text-gray-700 dark:text-gray-300 font-medium">Pengajuan User</span>
@endsection

@section('content')
    <div class="space-y-6" x-data="{
        approveOpen: false,
        rejectOpen: false,
        target: { id: null, name: '', role: 'user' },
        reason: '',
        approveUrlTemplate: @js(route('user-requests.approve', ['userRequest' => '__ID__'])),
        rejectUrlTemplate: @js(route('user-requests.reject', ['userRequest' => '__ID__'])),
        openApprove(id, name, role) {
            this.target = { id: id, name: name, role: role };
            this.approveOpen = true;
        },
        openReject(id, name) {
            this.target = { id: id, name: name, role: '' };
            this.reason = '';
            this.rejectOpen = true;
        }
    }">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h1 class="page-title">Pengajuan User Baru</h1>
                <p class="text-sm text-gray-500 mt-1">Setujui atau tolak pengajuan user baru dari Admin.</p>
            </div>
        </div>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="stat-card">
                <div class="stat-icon bg-orange-100 text-orange-600">⏳</div>
                <div>
                    <p class="text-xs text-gray-500">Menunggu Approval</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $summary['pending'] }}</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon bg-emerald-100 text-emerald-600">✅</div>
                <div>
                    <p class="text-xs text-gray-500">Disetujui</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $summary['approved'] }}</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon bg-red-100 text-red-600">❌</div>
                <div>
                    <p class="text-xs text-gray-500">Ditolak</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $summary['rejected'] }}</p>
                </div>
            </div>
        </div>

        {{-- Filter --}}
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('user-requests.index') }}"
                    class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                    <div>
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" onchange="this.form.submit()">
                            <option value="">Semua Status</option>
                            <option value="pending" @selected(request('status') == 'pending')>Menunggu</option>
                            <option value="approved" @selected(request('status') == 'approved')>Disetujui</option>
                            <option value="rejected" @selected(request('status') == 'rejected')>Ditolak</option>
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        @if (request()->filled('status'))
                            <a href="{{ route('user-requests.index') }}" class="btn btn-secondary">Reset</a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        {{-- Table --}}
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role Diusulkan</th>
                        <th>Diajukan Oleh</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($userRequests as $req)
                        <tr>
                            <td class="font-medium text-gray-900 dark:text-white">{{ $req->name }}</td>
                            <td>{{ $req->email }}</td>
                            <td>
                                <span class="badge badge-brand">{{ $req->role === 'admin' ? 'Admin' : 'User' }}</span>
                            </td>
                            <td>{{ $req->requestedBy->name ?? '-' }}</td>
                            <td>
                                @if ($req->status === 'pending')
                                    <span class="badge badge-warning">Menunggu</span>
                                @elseif ($req->status === 'approved')
                                    <span class="badge badge-success">Disetujui</span>
                                @else
                                    <span class="badge badge-danger">Ditolak</span>
                                @endif
                                @if ($req->status === 'rejected' && $req->reject_reason)
                                    <p class="text-xs text-gray-400 mt-1">{{ $req->reject_reason }}</p>
                                @endif
                            </td>
                            <td class="text-xs text-gray-500">
                                {{ $req->created_at->translatedFormat('d M Y H:i') }}</td>
                            <td class="text-right whitespace-nowrap">
                                @if ($req->status === 'pending')
                                    <button type="button" class="btn btn-success btn-xs"
                                        @click="openApprove({{ $req->id }}, '{{ addslashes($req->name) }}', '{{ $req->role }}')">
                                        Approve
                                    </button>
                                    <button type="button" class="btn btn-danger btn-xs"
                                        @click="openReject({{ $req->id }}, '{{ addslashes($req->name) }}')">
                                        Tolak
                                    </button>
                                @else
                                    <span class="text-xs text-gray-400">Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-gray-400 py-8">
                                Belum ada pengajuan user.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>{{ $userRequests->links() }}</div>

        {{-- Modal: Approve --}}
        <div class="modal-backdrop" x-show="approveOpen" x-cloak x-transition @click.self="approveOpen = false"
            style="display:none">
            <div class="modal-box" @click.stop>
                <form :action="approveUrlTemplate.replace('__ID__', target.id)" method="POST">
                    @csrf
                    <div class="card-header">
                        <p class="font-semibold text-gray-900 dark:text-white">Approve User</p>
                    </div>
                    <div class="card-body space-y-3">
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            Setujui <strong x-text="target.name"></strong> sebagai:
                        </p>
                        <div>
                            <label class="form-label">Role</label>
                            <select name="role" x-model="target.role" class="form-select">
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <p class="text-xs text-gray-400">User akan langsung aktif dan bisa login setelah disetujui.
                        </p>
                    </div>
                    <div class="card-footer flex justify-end gap-2">
                        <button type="button" class="btn btn-secondary" @click="approveOpen = false">Batal</button>
                        <button type="submit" class="btn btn-success">Approve</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal: Reject --}}
        <div class="modal-backdrop" x-show="rejectOpen" x-cloak x-transition @click.self="rejectOpen = false"
            style="display:none">
            <div class="modal-box" @click.stop>
                <form :action="rejectUrlTemplate.replace('__ID__', target.id)" method="POST">
                    @csrf
                    <div class="card-header">
                        <p class="font-semibold text-gray-900 dark:text-white">Tolak Pengajuan</p>
                    </div>
                    <div class="card-body space-y-3">
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            Tolak pengajuan <strong x-text="target.name"></strong>?
                        </p>
                        <div>
                            <label class="form-label">Alasan (opsional)</label>
                            <textarea name="reject_reason" x-model="reason" rows="3" class="form-textarea"></textarea>
                        </div>
                    </div>
                    <div class="card-footer flex justify-end gap-2">
                        <button type="button" class="btn btn-secondary" @click="rejectOpen = false">Batal</button>
                        <button type="submit" class="btn btn-danger">Tolak</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection
