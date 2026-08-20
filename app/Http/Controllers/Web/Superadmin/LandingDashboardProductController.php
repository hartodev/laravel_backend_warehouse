<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\LandingDashboardProduct;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LandingDashboardProductController extends Controller
{
    public const STATUSES = ['normal', 'low', 'critical'];

    public function index(): View
    {
        $products = LandingDashboardProduct::ordered()->paginate(15);

        return view('superadmin.landing-dashboard-products.index', compact('products'));
    }

    public function create(): View
    {
        return view('superadmin.landing-dashboard-products.create', ['statuses' => self::STATUSES]);
    }

    public function store(Request $request): RedirectResponse
    {
        LandingDashboardProduct::create($this->validated($request));

        return redirect()
            ->route('superadmin.landing-dashboard-products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(LandingDashboardProduct $landingDashboardProduct): View
    {
        return view('superadmin.landing-dashboard-products.edit', [
            'product'  => $landingDashboardProduct,
            'statuses' => self::STATUSES,
        ]);
    }

    public function update(Request $request, LandingDashboardProduct $landingDashboardProduct): RedirectResponse
    {
        $landingDashboardProduct->update($this->validated($request));

        return redirect()
            ->route('superadmin.landing-dashboard-products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(LandingDashboardProduct $landingDashboardProduct): RedirectResponse
    {
        $landingDashboardProduct->delete();

        return redirect()
            ->route('superadmin.landing-dashboard-products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'sku'       => ['required', 'string', 'max:50'],
            'stock'     => ['required', 'integer', 'min:0'],
            'status'    => ['required', 'string', 'in:' . implode(',', self::STATUSES)],
            'order'     => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['order'] = $data['order'] ?? 0;

        return $data;
    }
}
