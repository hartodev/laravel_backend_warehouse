<?php

namespace App\Http\Controllers\Web\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserCreationRequest;
use App\Models\Notification;
use Illuminate\Http\Request;

class UserCreationRequestController extends Controller
{
    // ── GET /superadmin/user-requests ────────────────────────────────────────
    public function index(Request $request)
    {
        $userRequests = UserCreationRequest::with(['requestedBy', 'approvedBy'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $summary = [
            'pending'  => UserCreationRequest::where('status', 'pending')->count(),
            'approved' => UserCreationRequest::where('status', 'approved')->count(),
            'rejected' => UserCreationRequest::where('status', 'rejected')->count(),
        ];

        return view('superadmin.user_request.index', compact('userRequests', 'summary'));
    }

    // ── PATCH /superadmin/user-requests/{userRequest} (ubah role sebelum approve) ──
    public function updateRole(Request $request, UserCreationRequest $userRequest)
    {
        if ($userRequest->status !== 'pending') {
            return back()->with('error', 'Pengajuan sudah diproses.');
        }

        $request->validate(['role' => 'required|in:user,admin']);

        $userRequest->update(['role' => $request->role]);

        return back()->with('success', 'Role diupdate.');
    }

    // ── POST /superadmin/user-requests/{userRequest}/approve ─────────────────
    public function approve(Request $request, UserCreationRequest $userRequest)
    {
        if ($userRequest->status !== 'pending') {
            return back()->with('error', 'Pengajuan sudah diproses.');
        }

        $request->validate(['role' => 'required|in:user,admin']);

        $user = User::create([
            'name'      => $userRequest->name,
            'email'     => $userRequest->email,
            'password'  => $userRequest->password, // sudah hashed
            'role'      => $request->role,
            'is_active' => true,
        ]);

        UserProfile::create([
            'user_id' => $user->id,
            'phone'   => $userRequest->phone,
            'address' => $userRequest->address,
        ]);

        $userRequest->update([
            'status'          => 'approved',
            'role'            => $request->role,
            'approved_by'     => auth()->id(),
            'created_user_id' => $user->id,
        ]);

        Notification::create([
            'user_id' => $userRequest->requested_by,
            'type'    => 'user_request_approved',
            'title'   => 'Pengajuan Disetujui',
            'body'    => "Pengajuan user {$userRequest->name} disetujui sebagai " .
                ($request->role === 'admin' ? 'Admin' : 'User') . '.',
            'data'    => ['user_request_id' => $userRequest->id],
        ]);

        return redirect()->route('superadmin.user-requests.index')
            ->with('success', "User {$userRequest->name} berhasil disetujui dan dibuat.");
    }

    // ── POST /superadmin/user-requests/{userRequest}/reject ──────────────────
    public function reject(Request $request, UserCreationRequest $userRequest)
    {
        if ($userRequest->status !== 'pending') {
            return back()->with('error', 'Pengajuan sudah diproses.');
        }

        $request->validate(['reject_reason' => 'nullable|string|max:255']);

        $userRequest->update([
            'status'        => 'rejected',
            'approved_by'   => auth()->id(),
            'reject_reason' => $request->reject_reason,
        ]);

        Notification::create([
            'user_id' => $userRequest->requested_by,
            'type'    => 'user_request_rejected',
            'title'   => 'Pengajuan Ditolak',
            'body'    => "Pengajuan user {$userRequest->name} ditolak." .
                ($request->reject_reason ? " Alasan: {$request->reject_reason}" : ''),
            'data'    => ['user_request_id' => $userRequest->id],
        ]);

        return redirect()->route('superadmin.user-requests.index')
            ->with('success', "Pengajuan {$userRequest->name} ditolak.");
    }
}


