<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExpenseReport;
use App\Services\ImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExpenseReportController extends Controller
{
     public function index(Request $request): JsonResponse
    {
        $reports = ExpenseReport::with(['budgetRequest:id,nomor_form,nama_item', 'submittedBy:id,name', 'verifiedBy:id,name'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->date_from, fn($q) => $q->whereDate('tanggal_transaksi', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('tanggal_transaksi', '<=', $request->date_to))
            ->latest()
            ->paginate($request->per_page ?? 15);

        return response()->json(['success' => true, 'data' => $reports]);
    }

    public function show(ExpenseReport $er): JsonResponse
    {
        $er->load(['budgetRequest', 'submittedBy:id,name', 'verifiedBy:id,name']);

        return response()->json(['success' => true, 'data' => $er]);
    }

    // public function store(Request $request): JsonResponse
    // {
    //     $validator = Validator::make($request->all(), [
    //         'budget_request_id'   => 'required|exists:budget_requests,id',
    //         'nomor_invoice'       => 'nullable|string|max:150',
    //         'nama_vendor'         => 'nullable|string|max:200',
    //         'tanggal_transaksi'   => 'required|date',
    //         'nominal_realisasi'   => 'required|numeric|min:0',
    //         'lamp_invoice'        => 'nullable|boolean',
    //         'lamp_bukti_transfer' => 'nullable|boolean',
    //         'lamp_kartu_garansi'  => 'nullable|boolean',
    //         'lamp_serah_terima'   => 'nullable|boolean',
    //         'lamp_lainnya'        => 'nullable|string',
    //         'catatan'             => 'nullable|string',
    //         'attachments.*'       => ImageService::documentRules(),
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
    //     }

    //     // Upload file lampiran
    //     $attachmentPaths = [];
    //     if ($request->hasFile('attachments')) {
    //         foreach ($request->file('attachments') as $file) {
    //             $attachmentPaths[] = ImageService::upload($file, 'expenses');
    //         }
    //     }

    //     $er = ExpenseReport::create([
    //         'budget_request_id'   => $request->budget_request_id,
    //         'submitted_by'        => auth()->id(),
    //         'nomor_invoice'       => $request->nomor_invoice,
    //         'nama_vendor'         => $request->nama_vendor,
    //         'tanggal_transaksi'   => $request->tanggal_transaksi,
    //         'nominal_realisasi'   => $request->nominal_realisasi,
    //         'selisih'             => 0, // dihitung setelah save
    //         'lamp_invoice'        => $request->boolean('lamp_invoice'),
    //         'lamp_bukti_transfer' => $request->boolean('lamp_bukti_transfer'),
    //         'lamp_kartu_garansi'  => $request->boolean('lamp_kartu_garansi'),
    //         'lamp_serah_terima'   => $request->boolean('lamp_serah_terima'),
    //         'lamp_lainnya'        => $request->lamp_lainnya,
    //         'attachment_files'    => $attachmentPaths,
    //         'catatan'             => $request->catatan,
    //         'status'              => 'submitted',
    //     ]);

    //     // Hitung selisih
    //     $er->calculateSelisih();
    //     $er->save();

    //     return response()->json(['success' => true, 'message' => 'Laporan pertanggungjawaban berhasil dikirim.', 'data' => $er], 201);
    // }


    use App\Services\ExpenseReportService;
    use App\Models\BudgetRequest;
    use Illuminate\Validation\ValidationException;

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'budget_request_id'   => 'required|exists:budget_requests,id',
            'nomor_invoice'       => 'nullable|string|max:150',
            'nama_vendor'         => 'nullable|string|max:200',
            'tanggal_transaksi'   => 'required|date',
            'nominal_realisasi'   => 'required|numeric|min:0',
            'lamp_invoice'        => 'nullable|boolean',
            'lamp_bukti_transfer' => 'nullable|boolean',
            'lamp_kartu_garansi'  => 'nullable|boolean',
            'lamp_serah_terima'   => 'nullable|boolean',
            'lamp_lainnya'        => 'nullable|string',
            'catatan'             => 'nullable|string',
            'attachments.*'       => ImageService::documentRules(),
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }

        $budgetRequest = BudgetRequest::findOrFail($request->budget_request_id);

        $attachmentPaths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $attachmentPaths[] = ImageService::upload($file, 'expenses');
            }
        }

        try {
            $er = ExpenseReportService::createFromRequest($budgetRequest, $validator->validated(), $attachmentPaths);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $e->errors()], 422);
        }

        $message = $er->status === 'pending_revisi'
            ? 'Nominal realisasi melebihi sisa anggaran. Laporan disimpan dan revisi anggaran otomatis diajukan, menunggu persetujuan.'
            : 'Laporan pertanggungjawaban berhasil dikirim & realisasi otomatis tercatat di buku kas.';

        return response()->json(['success' => true, 'message' => $message, 'data' => $er], 201);
    }


    public function update(Request $request, ExpenseReport $er): JsonResponse
    {
        if ($er->status === 'verified') {
            return response()->json(['success' => false, 'message' => 'Laporan yang sudah diverifikasi tidak dapat diubah.'], 422);
        }

        $er->update($request->only('nomor_invoice', 'nama_vendor', 'tanggal_transaksi', 'nominal_realisasi', 'lamp_invoice', 'lamp_bukti_transfer', 'lamp_kartu_garansi', 'lamp_serah_terima', 'lamp_lainnya', 'catatan'));

        $er->calculateSelisih();
        $er->save();

        return response()->json(['success' => true, 'message' => 'Laporan berhasil diupdate.', 'data' => $er->fresh()]);
    }

    // POST verify
    public function verify(Request $request, ExpenseReport $er): JsonResponse
    {
        if ($er->status !== 'submitted') {
            return response()->json(['success' => false, 'message' => 'Hanya laporan submitted yang dapat diverifikasi.'], 422);
        }

        $er->update([
            'status'      => 'verified',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
            'catatan'     => $request->catatan ?? $er->catatan,
        ]);

        return response()->json(['success' => true, 'message' => 'Laporan berhasil diverifikasi.', 'data' => $er->fresh()]);
    }
}
