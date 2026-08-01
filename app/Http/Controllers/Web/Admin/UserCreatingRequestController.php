<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserCreationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserCreationRequestController extends Controller
{
    /**
     * Daftar pengajuan user yang dibuat oleh admin yang sedang login.
     */
    public function index(Request $request)
    {
        $userRequests = UserCreationRequest::where('requested_by', Auth::id())
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('Admin.user_request.index', compact('userRequests'));
    }

    /**
     * Form pengajuan user baru.
     */
    public function create()
    {
        return view('Admin.user_request.create');
    }

    /**
     * Simpan pengajuan, status default 'pending' menunggu review superadmin.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:150',
            'email'    => 'required|email|max:150|unique:users,email|unique:user_creation_requests,email',
            'phone'    => 'nullable|string|max:30',
            'role'     => 'required|in:admin,staff,warehouse_keeper', // sesuaikan dengan role yang tersedia di sistem kamu
            'division' => 'nullable|string|max:100',
            'reason'   => 'required|string|max:1000',
        ]);

        $validated['requested_by'] = Auth::id();
        $validated['status'] = 'pending';

        UserCreationRequest::create($validated);

        return redirect()
            ->route('admin.user-requests.index')
            ->with('success', 'Pengajuan user berhasil dikirim, menunggu persetujuan Superadmin.');
    }

    /**
     * Detail satu pengajuan (termasuk status & alasan penolakan jika ada).
     */
    public function show(UserCreationRequest $userRequest)
    {
        // Admin hanya boleh melihat pengajuannya sendiri.
        abort_unless($userRequest->requested_by === Auth::id(), 403);

        return view('Admin.user_request.show', compact('userRequest'));
    }

    /**
     * Batalkan pengajuan selama masih berstatus pending.
     */
    public function destroy(UserCreationRequest $userRequest)
    {
        abort_unless($userRequest->requested_by === Auth::id(), 403);

        if ($userRequest->status !== 'pending') {
            return back()->with('error', 'Pengajuan yang sudah diproses tidak bisa dibatalkan.');
        }

        $userRequest->delete();

        return redirect()
            ->route('admin.user-requests.index')
            ->with('success', 'Pengajuan berhasil dibatalkan.');
    }
}