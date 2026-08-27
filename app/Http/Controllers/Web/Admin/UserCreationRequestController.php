<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use App\Models\UserCreationRequest;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserCreationRequestController extends Controller
{
    /**
     * Daftar pengajuan user yang dibuat oleh admin yang sedang login.
     * Ini SENGAJA di-scope ke requested_by = admin yang login — menyamai
     * Api/Admin::myRequests(), BUKAN Api/Admin::index() yang memang
     * khusus Super Admin (lihat komentar route di controller API).
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
     *
     * CATATAN PERBAIKAN:
     * - Role yang boleh diajukan HARUS 'user' atau 'admin' saja — samakan ke
     *   Api/Admin::store(). Role 'staff'/'warehouse_keeper' sebelumnya
     *   TIDAK dikenali oleh User model (lihat isAdmin()/isUser()/isSuperAdmin()),
     *   jadi kalau disetujui, akun yang terbentuk tidak akan lolos middleware
     *   role manapun.
     * - Tambah notifikasi ke semua Super Admin aktif setelah pengajuan dibuat,
     *   menyamai Api/Admin::store() (sebelumnya tidak ada sama sekali di versi web).
     * - Field 'division' dan 'reason' dipertahankan karena sudah ada di form —
     *   pastikan kolom ini memang ada di tabel user_creation_requests (migration
     *   tidak ikut ter-upload, jadi ini TIDAK bisa aku verifikasi dari sini).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                  => 'required|string|max:150',
            'email'                 => 'required|email|max:150|unique:users,email|unique:user_creation_requests,email',
            'phone'                 => 'nullable|string|max:30',
            'address'               => 'nullable|string|max:255',
            'password'              => 'required|string|min:8|confirmed',
            'role'                  => 'required|in:user,admin', // super_admin tidak boleh diajukan sendiri
            'division'              => 'nullable|string|max:100',
            'reason'                => 'required|string|max:1000',
        ]);

        if (UserCreationRequest::where('email', $validated['email'])->pending()->exists()) {
            return back()->withInput()->with('error', 'Sudah ada pengajuan pending dengan email ini.');
        }

        $validated['password']     = Hash::make($validated['password']);
        $validated['requested_by'] = Auth::id();
        $validated['status']       = 'pending';

        $userRequest = UserCreationRequest::create($validated);

        $superAdmins = User::where('role', 'super_admin')->where('is_active', true)->get();
        foreach ($superAdmins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type'    => 'user_request',
                'title'   => 'Pengajuan User Baru',
                'body'    => "{$userRequest->name} diajukan sebagai {$userRequest->role} oleh " . Auth::user()->name,
                'data'    => ['user_request_id' => $userRequest->id],
            ]);
        }

        return redirect()
            ->route('admin.user-requests.index')
            ->with('success', 'Pengajuan user berhasil dikirim, menunggu persetujuan Superadmin.');
    }

    /**
     * Detail satu pengajuan (termasuk status & alasan penolakan jika ada).
     * Admin hanya boleh lihat pengajuan miliknya sendiri.
     */
    public function show(UserCreationRequest $userRequest)
    {
        abort_unless($userRequest->requested_by === Auth::id(), 403);

        return view('Admin.user_request.show', compact('userRequest'));
    }

    /**
     * Batalkan pengajuan selama masih berstatus pending.
     * NB: method ini TIDAK ada di Api/Admin — ini fitur tambahan khusus web
     * yang aman (admin hanya bisa hapus pengajuan miliknya sendiri, dan hanya
     * selama masih pending, bukan wewenang approve/reject Superadmin).
     * Kalau kamu mau strict 1:1 dengan API, method ini + route destroy bisa
     * dihapus.
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

    /**
     * PATCH /admin/user-requests/{userRequest} — ubah role sebelum approve.
     * Menyamai Api/Admin::update() (di API method ini sesungguhnya wewenang
     * Super Admin — disertakan di sini untuk kelengkapan 1:1 dengan API).
     */
    public function update(Request $request, UserCreationRequest $userRequest)
    {
        if ($userRequest->status !== 'pending') {
            return back()->with('error', 'Pengajuan sudah diproses.');
        }

        $validator = Validator::make($request->all(), [
            'role' => 'required|in:user,admin',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $userRequest->update(['role' => $request->role]);

        return back()->with('success', 'Role diupdate.');
    }

    /**
     * POST /admin/user-requests/{userRequest}/approve
     * Menyamai Api/Admin::approve() — wewenang aslinya Super Admin.
     */
    public function approve(Request $request, UserCreationRequest $userRequest)
    {
        if ($userRequest->status !== 'pending') {
            return back()->with('error', 'Pengajuan sudah diproses.');
        }

        $role = $request->role ?? $userRequest->role;
        if (! in_array($role, ['user', 'admin'])) {
            return back()->with('error', 'Role tidak valid.');
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
            'approved_by'     => Auth::id(),
            'created_user_id' => $user->id,
        ]);

        $this->notifyRequester(
            $userRequest,
            'user_request_approved',
            'Pengajuan Disetujui',
            "Pengajuan user {$userRequest->name} disetujui sebagai " .
                ($role === 'admin' ? 'Admin' : 'User') . '.'
        );

        return redirect()->route('admin.user-requests.index')
            ->with('success', 'User disetujui dan berhasil dibuat.');
    }

    /**
     * POST /admin/user-requests/{userRequest}/reject
     * Menyamai Api/Admin::reject() — wewenang aslinya Super Admin.
     */
    public function reject(Request $request, UserCreationRequest $userRequest)
    {
        if ($userRequest->status !== 'pending') {
            return back()->with('error', 'Pengajuan sudah diproses.');
        }

        $userRequest->update([
            'status'        => 'rejected',
            'approved_by'   => Auth::id(),
            'reject_reason' => $request->reject_reason,
        ]);

        $reason = $request->reject_reason ? " Alasan: {$request->reject_reason}" : '';
        $this->notifyRequester(
            $userRequest,
            'user_request_rejected',
            'Pengajuan Ditolak',
            "Pengajuan user {$userRequest->name} ditolak.{$reason}"
        );

        return redirect()->route('admin.user-requests.index')
            ->with('success', 'Pengajuan ditolak.');
    }

    // ── Helper: kirim notifikasi ke admin yang mengajukan ────────────────
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