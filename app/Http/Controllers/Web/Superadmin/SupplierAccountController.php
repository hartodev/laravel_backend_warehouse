<?php

namespace App\Http\Controllers\Web\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/**
 * Controller BARU, terpisah dari Web\Superadmin\SupplierController yang
 * sudah ada — khusus mengelola akun login (role 'supplier' di tabel users,
 * terhubung lewat users.supplier_id) untuk supplier tertentu.
 *
 * SupplierController lama TIDAK diubah sama sekali. Untuk memicu halaman
 * create/destroy di sini, tambahkan link manual di
 * resources/views/superadmin/suppliers/show.blade.php — lihat catatan di
 * bawah file ini.
 */
class SupplierAccountController extends Controller
{
    // GET /superadmin/suppliers/{supplier}/account/create
    public function create(Supplier $supplier): View
    {
        abort_if($supplier->user()->exists(), 403, 'Supplier ini sudah punya akun login.');

        return view('superadmin.suppliers.account_create', compact('supplier'));
    }

    // POST /superadmin/suppliers/{supplier}/account
    public function store(Request $request, Supplier $supplier): RedirectResponse
    {
        abort_if($supplier->user()->exists(), 403, 'Supplier ini sudah punya akun login.');

        $validated = $request->validate([
            'email'    => 'required|email|max:150|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        User::create([
            'name'        => $supplier->name,
            'email'       => $validated['email'],
            'password'    => Hash::make($validated['password']),
            'role'        => 'supplier',
            'supplier_id' => $supplier->id,
            'is_active'   => true,
        ]);

        return redirect()->route('superadmin.suppliers.show', $supplier)
            ->with('success', 'Akun login supplier berhasil dibuat.');
    }

    // DELETE /superadmin/suppliers/{supplier}/account
    public function destroy(Supplier $supplier): RedirectResponse
    {
        $supplier->user()->delete();

        return back()->with('success', 'Akun login supplier dicabut.');
    }
}

/**
 * ── Tempel snippet ini secara manual ke resources/views/superadmin/suppliers/show.blade.php ──
 *
 * @if (!$supplier->user)
 *     <a href="{{ route('superadmin.suppliers.account.create', $supplier) }}" class="btn btn-sm btn-outline-primary">
 *         Buat Akun Login
 *     </a>
 * @else
 *     <form method="POST" action="{{ route('superadmin.suppliers.account.destroy', $supplier) }}"
 *           onsubmit="return confirm('Cabut akun login supplier ini?')" style="display:inline">
 *         @csrf
 *         @method('DELETE')
 *         <button type="submit" class="btn btn-sm btn-outline-danger">Cabut Akun Login</button>
 *     </form>
 * @endif
 */
