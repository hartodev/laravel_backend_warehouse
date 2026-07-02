<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\BudgetRequest;
use App\Models\BudgetRequestItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * Controller untuk USER mengajukan RAB (Rencana Anggaran Biaya)
 * Flow: draft → submit (pending) → admin review → super admin approve
 */
class UserBudgetRequestController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    // GET /api/user/budget-requests
    // Hanya milik user yang login
    // ─────────────────────────────────────────────────────────────
    public function index(Request $request): JsonResponse
    {
        $brs = BudgetRequest::with(['items'])
            ->where('user_id', auth()->id())
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->jenis, fn($q) => $q->where('jenis', $request->jenis))
            ->latest()
            ->paginate($request->per_page ?? 15);

        return response()->json(['success' => true, 'data' => $brs]);
    }

    // ─────────────────────────────────────────────────────────────
    // GET /api/user/budget-requests/{br}
    // ─────────────────────────────────────────────────────────────
    public function show(BudgetRequest $br): JsonResponse
    {
        // Pastikan hanya milik user yang login
        if ($br->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $br->load(['items', 'adminApprover:id,name', 'superAdminApprover:id,name']);

        return response()->json(['success' => true, 'data' => $br]);
    }

    // ─────────────────────────────────────────────────────────────
    // POST /api/user/budget-requests
    // User membuat pengajuan RAB baru (status: draft)
    // ─────────────────────────────────────────────────────────────
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'divisi'             => 'required|string|max:100',
            'tanggal_pengajuan'  => 'required|date',
            'jenis'              => 'required|in:rab,luar_rab',
            'kode_akun'          => 'nullable|string|max:50',
            'nama_akun'          => 'nullable|string|max:100',
            'keterangan'         => 'nullable|string',
            'alasan_luar_rab'    => 'required_if:jenis,luar_rab|nullable|string',
            'urgensi'            => 'nullable|in:normal,mendesak',
            'dampak_jika_tidak'  => 'nullable|string',
            'sumber_dana'        => 'nullable|in:realokasi,tambahan,lainnya',
            // Items wajib ada minimal 1
            'items'              => 'required|array|min:1',
            'items.*.nama_item'      => 'required|string|max:255',
            'items.*.qty'            => 'nullable|numeric|min:0',
            'items.*.satuan'         => 'nullable|string|max:50',
            'items.*.estimasi_biaya' => 'required|numeric|min:0',
            'items.*.keterangan'     => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        return DB::transaction(function () use ($request) {
            $nomorForm = BudgetRequest::generateNomorForm($request->jenis, $request->divisi);

            $br = BudgetRequest::create([
                'nomor_form'        => $nomorForm,
                'user_id'           => auth()->id(),
                'divisi'            => $request->divisi,
                'tanggal_pengajuan' => $request->tanggal_pengajuan,
                'jenis'             => $request->jenis,
                'kode_akun'         => $request->kode_akun,
                'nama_akun'         => $request->nama_akun,
                'keterangan'        => $request->keterangan,
                'alasan_luar_rab'   => $request->alasan_luar_rab,
                'urgensi'           => $request->urgensi ?? 'normal',
                'dampak_jika_tidak' => $request->dampak_jika_tidak,
                'sumber_dana'       => $request->sumber_dana,
                'status'            => 'draft',
                'total_estimasi'    => 0,
            ]);

            $totalEstimasi = 0;

            foreach ($request->items as $item) {
                $qty   = $item['qty'] ?? 1;
                $harga = $item['estimasi_biaya'];
                $total = $qty * $harga;
                $totalEstimasi += $total;

                BudgetRequestItem::create([
                    'budget_request_id' => $br->id,
                    'nama_item'         => $item['nama_item'],
                    'qty'               => $qty,
                    'satuan'            => $item['satuan'] ?? null,
                    'estimasi_biaya'    => $harga,
                    'total'             => $total,
                    'keterangan'        => $item['keterangan'] ?? null,
                ]);
            }

            $br->update(['total_estimasi' => $totalEstimasi]);

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan RAB berhasil dibuat.',
                'data'    => $br->load('items'),
            ], 201);
        });
    }

    // ─────────────────────────────────────────────────────────────
    // PUT /api/user/budget-requests/{br}
    // Hanya bisa edit jika masih draft
    // ─────────────────────────────────────────────────────────────
    public function update(Request $request, BudgetRequest $br): JsonResponse
    {
        if ($br->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        if ($br->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya pengajuan draft yang dapat diubah.',
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'divisi'             => 'sometimes|string|max:100',
            'tanggal_pengajuan'  => 'sometimes|date',
            'kode_akun'          => 'nullable|string|max:50',
            'nama_akun'          => 'nullable|string|max:100',
            'keterangan'         => 'nullable|string',
            'alasan_luar_rab'    => 'nullable|string',
            'urgensi'            => 'nullable|in:normal,mendesak',
            'dampak_jika_tidak'  => 'nullable|string',
            'sumber_dana'        => 'nullable|in:realokasi,tambahan,lainnya',
            'items'              => 'sometimes|array|min:1',
            'items.*.nama_item'      => 'required_with:items|string|max:255',
            'items.*.qty'            => 'nullable|numeric|min:0',
            'items.*.satuan'         => 'nullable|string|max:50',
            'items.*.estimasi_biaya' => 'required_with:items|numeric|min:0',
            'items.*.keterangan'     => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        return DB::transaction(function () use ($request, $br) {
            $br->update($request->only(
                'divisi', 'tanggal_pengajuan', 'kode_akun', 'nama_akun',
                'keterangan', 'alasan_luar_rab', 'urgensi', 'dampak_jika_tidak', 'sumber_dana'
            ));

            if ($request->has('items')) {
                // Hapus items lama, buat ulang
                $br->items()->delete();
                $totalEstimasi = 0;

                foreach ($request->items as $item) {
                    $qty   = $item['qty'] ?? 1;
                    $harga = $item['estimasi_biaya'];
                    $total = $qty * $harga;
                    $totalEstimasi += $total;

                    BudgetRequestItem::create([
                        'budget_request_id' => $br->id,
                        'nama_item'         => $item['nama_item'],
                        'qty'               => $qty,
                        'satuan'            => $item['satuan'] ?? null,
                        'estimasi_biaya'    => $harga,
                        'total'             => $total,
                        'keterangan'        => $item['keterangan'] ?? null,
                    ]);
                }

                $br->update(['total_estimasi' => $totalEstimasi]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan RAB berhasil diupdate.',
                'data'    => $br->fresh()->load('items'),
            ]);
        });
    }

    // ─────────────────────────────────────────────────────────────
    // DELETE /api/user/budget-requests/{br}
    // Hanya bisa hapus jika masih draft
    // ─────────────────────────────────────────────────────────────
    public function destroy(BudgetRequest $br): JsonResponse
    {
        if ($br->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        if ($br->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya pengajuan draft yang dapat dihapus.',
            ], 422);
        }

        $br->delete();

        return response()->json(['success' => true, 'message' => 'Pengajuan RAB berhasil dihapus.']);
    }

    // ─────────────────────────────────────────────────────────────
    // POST /api/user/budget-requests/{br}/submit
    // User submit → status: pending (masuk ke antrian admin)
    // ─────────────────────────────────────────────────────────────
    public function submit(BudgetRequest $br): JsonResponse
    {
        if ($br->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        if ($br->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya pengajuan draft yang dapat disubmit.',
            ], 422);
        }

        if ($br->items()->count() === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Pengajuan harus memiliki minimal 1 item sebelum disubmit.',
            ], 422);
        }

        $br->update(['status' => 'pending']);

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan RAB berhasil dikirim. Menunggu persetujuan admin.',
            'data'    => $br->fresh()->load('items'),
        ]);
    }
}








