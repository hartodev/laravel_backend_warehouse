<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\BudgetRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BudgetRequestController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $brs = BudgetRequest::with(['user:id,name', 'branchManager:id,name', 'finance:id,name'])
            ->when($request->jenis, fn($q) => $q->where('jenis', $request->jenis))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->divisi, fn($q) => $q->where('divisi', 'like', "%{$request->divisi}%"))
            ->when($request->date_from, fn($q) => $q->whereDate('tanggal_pengajuan', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('tanggal_pengajuan', '<=', $request->date_to))
            ->latest()
            ->paginate($request->per_page ?? 15);
 
        return response()->json(['success' => true, 'data' => $brs]);
    }
 
    public function show(BudgetRequest $br): JsonResponse
    {
        $br->load(['user:id,name', 'branchManager:id,name', 'finance:id,name', 'verification', 'expenseReport', 'revision']);
 
        return response()->json(['success' => true, 'data' => $br]);
    }
 
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'divisi'           => 'required|string|max:100',
            'tanggal_pengajuan'=> 'required|date',
            'jenis'            => 'required|in:rab,luar_rab',
            'kode_akun'        => 'nullable|string|max:50',
            'nama_akun'        => 'nullable|string|max:100',
            'nama_item'        => 'required|string|max:255',
            'qty'              => 'nullable|numeric|min:0',
            'satuan'           => 'nullable|string|max:50',
            'estimasi_biaya'   => 'required|numeric|min:0',
            'keterangan'       => 'nullable|string',
            'alasan_luar_rab'  => 'required_if:jenis,luar_rab|nullable|string',
            'urgensi'          => 'nullable|in:normal,mendesak',
            'dampak_jika_tidak'=> 'nullable|string',
            'sumber_dana'      => 'nullable|in:realokasi,tambahan,lainnya',
        ]);
 
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }
 
        $nomorForm = BudgetRequest::generateNomorForm($request->jenis, $request->divisi);
 
        $br = BudgetRequest::create([
            'nomor_form'        => $nomorForm,
            'user_id'           => auth()->id(),
            'divisi'            => $request->divisi,
            'tanggal_pengajuan' => $request->tanggal_pengajuan,
            'jenis'             => $request->jenis,
            'kode_akun'         => $request->kode_akun,
            'nama_akun'         => $request->nama_akun,
            'nama_item'         => $request->nama_item,
            'qty'               => $request->qty,
            'satuan'            => $request->satuan,
            'estimasi_biaya'    => $request->estimasi_biaya,
            'total'             => $request->estimasi_biaya, // bisa diubah
            'keterangan'        => $request->keterangan,
            'alasan_luar_rab'   => $request->alasan_luar_rab,
            'urgensi'           => $request->urgensi ?? 'normal',
            'dampak_jika_tidak' => $request->dampak_jika_tidak,
            'sumber_dana'       => $request->sumber_dana,
            'status'            => 'draft',
        ]);
 
        return response()->json(['success' => true, 'message' => 'Pengajuan anggaran berhasil dibuat.', 'data' => $br], 201);
    }
 
    public function update(Request $request, BudgetRequest $br): JsonResponse
    {
        if (! in_array($br->status, ['draft', 'pending'])) {
            return response()->json(['success' => false, 'message' => 'Pengajuan yang sudah diproses tidak dapat diubah.'], 422);
        }
 
        $br->update($request->only('divisi', 'nama_item', 'qty', 'satuan', 'estimasi_biaya', 'keterangan', 'alasan_luar_rab', 'urgensi', 'dampak_jika_tidak', 'sumber_dana'));
 
        return response()->json(['success' => true, 'message' => 'Pengajuan berhasil diupdate.', 'data' => $br->fresh()]);
    }
 
    public function destroy(BudgetRequest $br): JsonResponse
    {
        if ($br->status !== 'draft') {
            return response()->json(['success' => false, 'message' => 'Hanya pengajuan draft yang dapat dihapus.'], 422);
        }
 
        $br->delete();
 
        return response()->json(['success' => true, 'message' => 'Pengajuan berhasil dihapus.']);
    }
 
    // POST submit — kirim ke branch manager
    public function submit(BudgetRequest $br): JsonResponse
    {
        if ($br->status !== 'draft') {
            return response()->json(['success' => false, 'message' => 'Hanya pengajuan draft yang dapat disubmit.'], 422);
        }
 
        $br->update(['status' => 'pending']);
 
        return response()->json(['success' => true, 'message' => 'Pengajuan berhasil dikirim untuk persetujuan.', 'data' => $br->fresh()]);
    }
 
    // POST approve — branch manager approve
    public function approve(Request $request, BudgetRequest $br): JsonResponse
    {
        if (! in_array($br->status, ['pending', 'pending_finance'])) {
            return response()->json(['success' => false, 'message' => 'Status pengajuan tidak sesuai untuk disetujui.'], 422);
        }
 
        $br->update([
            'status'                  => 'approved',
            'branch_manager_id'       => auth()->id(),
            'branch_manager_at'       => now(),
            'catatan_branch_manager'  => $request->catatan,
        ]);
 
        return response()->json(['success' => true, 'message' => 'Pengajuan anggaran disetujui.', 'data' => $br->fresh()]);
    }
 
    // POST reject — branch manager reject
    public function reject(Request $request, BudgetRequest $br): JsonResponse
    {
        $validator = Validator::make($request->all(), ['reject_reason' => 'required|string']);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Alasan penolakan wajib diisi.', 'errors' => $validator->errors()], 422);
        }
 
        $br->update([
            'status'                 => 'ditolak',
            'branch_manager_id'      => auth()->id(),
            'branch_manager_at'      => now(),
            'catatan_branch_manager' => $request->reject_reason,
        ]);
 
        return response()->json(['success' => true, 'message' => 'Pengajuan anggaran ditolak.', 'data' => $br->fresh()]);
    }
}
