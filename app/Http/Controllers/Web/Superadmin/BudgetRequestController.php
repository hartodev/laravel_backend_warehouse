<?php

namespace App\Http\Controllers\Web\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\BudgetRequest;
use App\Services\CashBookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Web controller — Super Admin panel
 *
 * Flow sesuai konsep baru:
 *   draft       → (user, via mobile app)
 *   pending     → (admin review, via mobile app)
 *   pending_sa  → SUPER ADMIN approve final → approved (dana dialokasikan ke cash_book)
 *   pending_sa  → SUPER ADMIN reject        → ditolak
 *   approved    → SUPER ADMIN catat realisasi (dana terpakai)
 *
 * Super admin punya akses penuh (lihat semua RAB dari semua role),
 * tapi action utamanya adalah approve, reject, dan realisasi.
 * Create/store manual tetap tersedia untuk kasus input langsung oleh SA.
 */
class BudgetRequestController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    // GET /superadmin/budget-requests
    // Full akses — lihat semua RAB dari semua status & role
    // ─────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $brs = BudgetRequest::with(['user:id,name', 'adminApprover:id,name', 'superAdminApprover:id,name'])
            ->withCount('items')
            ->when($request->jenis, fn($q) => $q->where('jenis', $request->jenis))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->urgensi, fn($q) => $q->where('urgensi', $request->urgensi))
            ->when($request->divisi, fn($q) => $q->where('divisi', 'like', "%{$request->divisi}%"))
            ->when($request->date_from, fn($q) => $q->whereDate('tanggal_pengajuan', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('tanggal_pengajuan', '<=', $request->date_to))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        // Ringkasan keuangan untuk dashboard widget di halaman index
        $summary = [
            'menunggu_admin'  => BudgetRequest::where('status', 'pending')->count(),
            'menunggu_sa'     => BudgetRequest::where('status', 'pending_sa')->count(),
            'total_disetujui' => BudgetRequest::where('status', 'approved')->count(),
            'total_anggaran'  => BudgetRequest::where('status', 'approved')->sum('total_estimasi'),
            'total_realisasi' => BudgetRequest::where('status', 'approved')->sum('total_realisasi'),
            'sisa_anggaran'   => BudgetRequest::where('status', 'approved')
                ->selectRaw('COALESCE(SUM(total_estimasi - total_realisasi), 0) as sisa')
                ->value('sisa'),
            'mendesak_pending' => BudgetRequest::whereIn('status', ['pending', 'pending_sa'])
                ->where('urgensi', 'mendesak')
                ->count(),
        ];

        return view('superadmin.budget_request.index', compact('brs', 'summary'));
    }

    // ─────────────────────────────────────────────────────────────
    // GET /superadmin/budget-requests/create
    // Opsional — SA bisa input manual jika diperlukan
    // ─────────────────────────────────────────────────────────────
    public function create()
    {
        return view('superadmin.budget_request.create');
    }

    // ─────────────────────────────────────────────────────────────
    // POST /superadmin/budget-requests
    // ─────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'divisi'            => 'required|string|max:100',
            'tanggal_pengajuan' => 'required|date',
            'jenis'             => 'required|in:rab,luar_rab',
            'kode_akun'         => 'nullable|string|max:50',
            'nama_akun'         => 'nullable|string|max:100',
            'keterangan'        => 'nullable|string',
            'alasan_luar_rab'   => 'required_if:jenis,luar_rab|nullable|string',
            'urgensi'           => 'nullable|in:normal,mendesak',
            'dampak_jika_tidak' => 'nullable|string',
            'sumber_dana'       => 'nullable|in:realokasi,tambahan,lainnya',

            'items'                  => 'required|array|min:1',
            'items.*.nama_item'      => 'required|string|max:255',
            'items.*.qty'            => 'nullable|numeric|min:0',
            'items.*.satuan'         => 'nullable|string|max:50',
            'items.*.estimasi_biaya' => 'required|numeric|min:0',
            'items.*.keterangan'     => 'nullable|string',
        ]);

        $nomorForm = BudgetRequest::generateNomorForm($validated['jenis'], $validated['divisi']);

        $budgetRequest = BudgetRequest::create([
            'nomor_form'        => $nomorForm,
            'user_id'           => auth()->id(),   // SA tercatat sebagai pengaju jika input manual
            'divisi'            => $validated['divisi'],
            'tanggal_pengajuan' => $validated['tanggal_pengajuan'],
            'jenis'             => $validated['jenis'],
            'kode_akun'         => $validated['kode_akun'] ?? null,
            'nama_akun'         => $validated['nama_akun'] ?? null,
            'keterangan'        => $validated['keterangan'] ?? null,
            'alasan_luar_rab'   => $validated['alasan_luar_rab'] ?? null,
            'urgensi'           => $validated['urgensi'] ?? 'normal',
            'dampak_jika_tidak' => $validated['dampak_jika_tidak'] ?? null,
            'sumber_dana'       => $validated['sumber_dana'] ?? null,
            'status'            => 'draft',
            'total_estimasi'    => 0,
            'total_realisasi'   => 0,
        ]);

        $totalEstimasi = $this->syncItems($budgetRequest, $validated['items']);
        $budgetRequest->update(['total_estimasi' => $totalEstimasi]);

        return redirect()->route('superadmin.budget-requests.index')
            ->with('success', 'Pengajuan anggaran berhasil dibuat.');
    }

    // ─────────────────────────────────────────────────────────────
    // GET /superadmin/budget-requests/{budgetRequest}
    // ─────────────────────────────────────────────────────────────
    public function show(BudgetRequest $budgetRequest)
    {
        $budgetRequest->load([
            'user:id,name,email',
            'adminApprover:id,name',
            'superAdminApprover:id,name',
            'items',
            'cashBooks',
        ]);

        $sisaAnggaran = $budgetRequest->total_estimasi - ($budgetRequest->total_realisasi ?? 0);

        return view('superadmin.budget_request.show', compact('budgetRequest', 'sisaAnggaran'));
    }

    // ─────────────────────────────────────────────────────────────
    // GET /superadmin/budget-requests/{budgetRequest}/edit
    // Hanya bisa edit jika belum diproses (draft/pending) — jarang dipakai SA
    // ─────────────────────────────────────────────────────────────
    public function edit(BudgetRequest $budgetRequest)
    {
        if (! in_array($budgetRequest->status, ['draft', 'pending'])) {
            return back()->with('error', 'Pengajuan yang sudah diproses tidak dapat diubah.');
        }

        $budgetRequest->load('items');
        return view('superadmin.budget_request.edit', compact('budgetRequest'));
    }

    // ─────────────────────────────────────────────────────────────
    // PUT /superadmin/budget-requests/{budgetRequest}
    // ─────────────────────────────────────────────────────────────
    public function update(Request $request, BudgetRequest $budgetRequest)
    {
        if (! in_array($budgetRequest->status, ['draft', 'pending'])) {
            return back()->with('error', 'Pengajuan yang sudah diproses tidak dapat diubah.');
        }

        $validated = $request->validate([
            'divisi'            => 'required|string|max:100',
            'keterangan'        => 'nullable|string',
            'alasan_luar_rab'   => 'required_if:jenis,luar_rab|nullable|string',
            'urgensi'           => 'nullable|in:normal,mendesak',
            'dampak_jika_tidak' => 'nullable|string',
            'sumber_dana'       => 'nullable|in:realokasi,tambahan,lainnya',

            'items'                  => 'required|array|min:1',
            'items.*.nama_item'      => 'required|string|max:255',
            'items.*.qty'            => 'nullable|numeric|min:0',
            'items.*.satuan'         => 'nullable|string|max:50',
            'items.*.estimasi_biaya' => 'required|numeric|min:0',
            'items.*.keterangan'     => 'nullable|string',
        ]);

        $budgetRequest->update($request->only(
            'divisi', 'keterangan', 'alasan_luar_rab', 'urgensi', 'dampak_jika_tidak', 'sumber_dana'
        ));

        $budgetRequest->items()->delete();
        $totalEstimasi = $this->syncItems($budgetRequest, $validated['items']);
        $budgetRequest->update(['total_estimasi' => $totalEstimasi]);

        return redirect()->route('superadmin.budget-requests.show', $budgetRequest)
            ->with('success', 'Pengajuan berhasil diupdate.');
    }

    // ─────────────────────────────────────────────────────────────
    // DELETE /superadmin/budget-requests/{budgetRequest}
    // ─────────────────────────────────────────────────────────────
    public function destroy(BudgetRequest $budgetRequest)
    {
        if ($budgetRequest->status !== 'draft') {
            return back()->with('error', 'Hanya pengajuan draft yang dapat dihapus.');
        }

        $budgetRequest->delete();

        return redirect()->route('superadmin.budget-requests.index')
            ->with('success', 'Pengajuan berhasil dihapus.');
    }

    // ─────────────────────────────────────────────────────────────
    // POST /superadmin/budget-requests/{budgetRequest}/approve
    // ★ ACTION UTAMA — approval final + catat dana ke cash_book
    // Hanya berlaku untuk status 'pending_sa' (sudah di-approve admin)
    // ─────────────────────────────────────────────────────────────
    public function approve(Request $request, BudgetRequest $budgetRequest)
    {
        if ($budgetRequest->status !== 'pending_sa') {
            return back()->with('error', 'Hanya pengajuan dengan status "Menunggu Super Admin" yang dapat disetujui di sini.');
        }

        // ★ Gate verifikasi — RAB harus sudah diverifikasi dengan
        // rekomendasi 'setuju' sebelum bisa di-approve final.
        $sudahDiverifikasiSetuju = $budgetRequest->budgetVerifications()
            ->where('rekomendasi', 'setuju')
            ->exists();

        if (! $sudahDiverifikasiSetuju) {
            return back()->with('error', 'RAB ini belum diverifikasi (atau belum direkomendasikan "setuju") oleh tahap Verifikasi Anggaran. Selesaikan verifikasi terlebih dahulu sebelum approve final.');
        }

        $request->validate([
            'catatan' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($request, $budgetRequest) {
            $budgetRequest->update([
                'status'     => 'approved',
                'finance_id' => auth()->id(),
                'finance_at' => now(),
            ]);

            // Catat alokasi dana — dana resmi tersedia setelah approval final
            CashBookService::record([
                'tanggal'           => now()->toDateString(),
                'keterangan'        => "Alokasi RAB #{$budgetRequest->nomor_form} — {$budgetRequest->divisi}",
                'jenis'             => 'alokasi_rab',
                'jumlah_uang'       => $budgetRequest->total_estimasi,
                'pihak'             => $budgetRequest->divisi,
                'tipe'              => 'masuk',
                'budget_request_id' => $budgetRequest->id,
                'created_by'        => auth()->id(),
                'catatan'           => $request->catatan,
            ]);
        });

        return back()->with('success', 'Pengajuan anggaran disetujui. Dana sebesar Rp ' .
            number_format($budgetRequest->total_estimasi, 0, ',', '.') . ' telah dialokasikan.');
    }

    // ─────────────────────────────────────────────────────────────
    // POST /superadmin/budget-requests/{budgetRequest}/reject
    // ─────────────────────────────────────────────────────────────
    public function reject(Request $request, BudgetRequest $budgetRequest)
    {
        if ($budgetRequest->status !== 'pending_sa') {
            return back()->with('error', 'Hanya pengajuan dengan status "Menunggu Super Admin" yang dapat ditolak di sini.');
        }

        $request->validate(['reject_reason' => 'required|string|max:500']);

        $budgetRequest->update([
            'status'                 => 'ditolak',
            'finance_id'             => auth()->id(),
            'finance_at'             => now(),
            'catatan_branch_manager' => trim(($budgetRequest->catatan_branch_manager ?? '') . ' | SA: ' . $request->reject_reason),
        ]);

        return back()->with('success', 'Pengajuan anggaran ditolak.');
    }

    // ─────────────────────────────────────────────────────────────
    // POST /superadmin/budget-requests/{budgetRequest}/realisasi
    // ★ ACTION UTAMA — catat dana yang sudah dipakai
    // ─────────────────────────────────────────────────────────────
    public function realisasi(Request $request, BudgetRequest $budgetRequest)
    {
        if ($budgetRequest->status !== 'approved') {
            return back()->with('error', 'Hanya RAB yang sudah disetujui yang dapat dicatat realisasinya.');
        }

        $request->validate([
            'jumlah_uang' => 'required|numeric|min:1',
            'keterangan'  => 'required|string|max:255',
            'tanggal'     => 'required|date',
        ]);

        $sisaAnggaran = $budgetRequest->total_estimasi - ($budgetRequest->total_realisasi ?? 0);

        if ($request->jumlah_uang > $sisaAnggaran) {
            return back()->with('error', 'Jumlah realisasi melebihi sisa anggaran (Rp ' .
                number_format($sisaAnggaran, 0, ',', '.') . ').');
        }

        DB::transaction(function () use ($request, $budgetRequest) {
            $budgetRequest->increment('total_realisasi', $request->jumlah_uang);

            CashBookService::record([
                'tanggal'           => $request->tanggal,
                'keterangan'        => "Realisasi RAB #{$budgetRequest->nomor_form} — {$request->keterangan}",
                'jenis'             => 'realisasi_rab',
                'jumlah_uang'       => $request->jumlah_uang,
                'pihak'             => $budgetRequest->divisi,
                'tipe'              => 'keluar',
                'budget_request_id' => $budgetRequest->id,
                'created_by'        => auth()->id(),
            ]);
        });

        $budgetRequest->refresh();
        $sisa = $budgetRequest->total_estimasi - $budgetRequest->total_realisasi;

        return back()->with('success', 'Realisasi dana berhasil dicatat. Sisa anggaran: Rp ' .
            number_format($sisa, 0, ',', '.'));
    }

    // ─────────────────────────────────────────────────────────────
    // Helper — simpan array items, return total estimasi
    // ─────────────────────────────────────────────────────────────
    private function syncItems(BudgetRequest $budgetRequest, array $items): float
    {
        $totalEstimasi = 0;

        foreach ($items as $item) {
            $qty   = $item['qty'] ?? 1;
            $total = $qty * $item['estimasi_biaya'];

            $budgetRequest->items()->create([
                'nama_item'      => $item['nama_item'],
                'qty'            => $item['qty'] ?? null,
                'satuan'         => $item['satuan'] ?? null,
                'estimasi_biaya' => $item['estimasi_biaya'],
                'total'          => $total,
                'keterangan'     => $item['keterangan'] ?? null,
            ]);

            $totalEstimasi += $total;
        }

        return $totalEstimasi;
    }
}
