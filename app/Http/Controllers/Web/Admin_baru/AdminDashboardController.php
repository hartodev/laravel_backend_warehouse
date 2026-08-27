<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Ganti dengan query asli ke model Stok/Transfer/Permintaan/Zona
        return view('admin.dashboard', [
            'totalSku'            => 1284,
            'stokMenipis'         => 37,
            'transferBerjalan'    => 12,
            'permintaanMenunggu'  => 8,
            // 'zonaGudang'  => Zona::withCount('items')->get(),
            // 'stokKritis'  => Stok::where('jumlah', '<', 'ambang_minimum')->get(),
            // 'aktivitas'   => ActivityLog::latest()->take(5)->get(),
        ]);
    }
}