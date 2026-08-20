<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserCreationRequest;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserCreationRequestController extends Controller
{
    // ── GET /api/superadmin/user-requests (super admin, semua pengajuan) ────
    public function index(Request $request): JsonResponse
    {
        $query = UserCreationRequest::with(['requestedBy', 'approvedBy'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate($request->per_page ?? 15);

        return response()->json(['success' => true, 'data' => $query]);
    }

    // ── GET /api/admin/user-requests/mine (admin, pengajuan miliknya) ───────
    public function myRequests(Request $request): JsonResponse
    {
        $query = UserCreationRequest::where('requested_by', auth()->id())
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate($request->per_page ?? 15);

        return response()->json(['success' => true, 'data' => $query]);
    }

    // ── POST /api/admin/user-requests (admin mengajukan user baru) ──────────
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'required|in:user,admin', // super_admin tidak boleh diajukan
            'phone'    => 'nullable|string|max:20',
            'address'  => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        if (UserCreationRequest::where('email', $request->email)->pending()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Sudah ada pengajuan pending dengan email ini.',
            ], 422);
        }

        $req = UserCreationRequest::create([
            'name'         => $request->name,
            'email'        => $request->email,
            'phone'        => $request->phone,
            'address'      => $request->address,
            'password'     => Hash::make($request->password),
            'role'         => $request->role,
            'status'       => 'pending',
            'requested_by' => auth()->id(),
        ]);

        // Notifikasi ke semua Super Admin
        $superAdmins = User::where('role', 'super_admin')
            ->where('is_active', true)
            ->get();

        foreach ($superAdmins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type'    => 'user_request',
                'title'   => 'Pengajuan User Baru',
                'body'    => "{$request->name} diajukan sebagai {$request->role} oleh " . auth()->user()->name,
                'data'    => ['user_request_id' => $req->id],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan user berhasil dikirim, menunggu approval Super Admin.',
            'data'    => $req,
        ], 201);
    }

    // ── PATCH /api/superadmin/user-requests/{id} (ubah role sebelum approve) ─
    public function update(Request $request, UserCreationRequest $userRequest): JsonResponse
    {
        if ($userRequest->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Pengajuan sudah diproses.'], 422);
        }

        $validator = Validator::make($request->all(), [
            'role' => 'required|in:user,admin',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $userRequest->update(['role' => $request->role]);

        return response()->json(['success' => true, 'message' => 'Role diupdate.', 'data' => $userRequest]);
    }

    // ── POST /api/superadmin/user-requests/{id}/approve ──────────────────────
    public function approve(Request $request, UserCreationRequest $userRequest): JsonResponse
    {
        if ($userRequest->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Pengajuan sudah diproses.'], 422);
        }

        $role = $request->role ?? $userRequest->role;
        if (!in_array($role, ['user', 'admin'])) {
            return response()->json(['success' => false, 'message' => 'Role tidak valid.'], 422);
        }

        $user = User::create([
            'name'      => $userRequest->name,
            'email'     => $userRequest->email,
            'password'  => $userRequest->password, // sudah hashed
            'role'      => $role,
            'is_active' => true,
        ]);

        UserProfile::create([
            'user_id' => $user->id,
            'phone'   => $userRequest->phone,
            'address' => $userRequest->address,
        ]);

        $userRequest->update([
            'status'          => 'approved',
            'role'            => $role,
            'approved_by'     => auth()->id(),
            'created_user_id' => $user->id,
        ]);

        // Notifikasi balik ke admin yang mengajukan
        $this->notifyRequester(
            $userRequest,
            'user_request_approved',
            'Pengajuan Disetujui',
            "Pengajuan user {$userRequest->name} disetujui sebagai " .
                ($role === 'admin' ? 'Admin' : 'User') . '.'
        );

        return response()->json([
            'success' => true,
            'message' => 'User disetujui dan berhasil dibuat.',
            'data'    => $user,
        ]);
    }

    // ── POST /api/superadmin/user-requests/{id}/reject ───────────────────────
    public function reject(Request $request, UserCreationRequest $userRequest): JsonResponse
    {
        if ($userRequest->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Pengajuan sudah diproses.'], 422);
        }

        $userRequest->update([
            'status'        => 'rejected',
            'approved_by'   => auth()->id(),
            'reject_reason' => $request->reject_reason,
        ]);

        // Notifikasi balik ke admin yang mengajukan
        $reason = $request->reject_reason ? " Alasan: {$request->reject_reason}" : '';
        $this->notifyRequester(
            $userRequest,
            'user_request_rejected',
            'Pengajuan Ditolak',
            "Pengajuan user {$userRequest->name} ditolak.{$reason}"
        );

        return response()->json(['success' => true, 'message' => 'Pengajuan ditolak.']);
    }

    // ── Helper: kirim notifikasi ke admin yang mengajukan ────────────────────
    private function notifyRequester(
        UserCreationRequest $userRequest,
        string $type,
        string $title,
        string $body
    ): void {
        Notification::create([
            'user_id' => $userRequest->requested_by,
            'type'    => $type,
            'title'   => $title,
            'body'    => $body,
            'data'    => ['user_request_id' => $userRequest->id],
        ]);
    }
}
