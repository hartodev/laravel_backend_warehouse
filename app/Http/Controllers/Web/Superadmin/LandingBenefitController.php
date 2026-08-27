<?php

namespace App\Http\Controllers\web\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\LandingBenefit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LandingBenefitController extends Controller
{
    public function index(): View
    {
        $benefits = LandingBenefit::ordered()->paginate(10);

        return view('superadmin.landing-benefits.index', compact('benefits'));
    }

    public function create(): View
    {
        return view('superadmin.landing-benefits.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        LandingBenefit::create($data);

        return redirect()
            ->route('superadmin.landing-benefits.index')
            ->with('success', 'Benefit berhasil ditambahkan.');
    }

    public function edit(LandingBenefit $landingBenefit): View
    {
        return view('superadmin.landing-benefits.edit', ['benefit' => $landingBenefit]);
    }

    public function update(Request $request, LandingBenefit $landingBenefit): RedirectResponse
    {
        $data = $this->validated($request);

        $landingBenefit->update($data);

        return redirect()
            ->route('superadmin.landing-benefits.index')
            ->with('success', 'Benefit berhasil diperbarui.');
    }

    public function destroy(LandingBenefit $landingBenefit): RedirectResponse
    {
        $landingBenefit->delete();

        return redirect()
            ->route('superadmin.landing-benefits.index')
            ->with('success', 'Benefit berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title'          => ['required', 'string', 'max:255'],
            'description'    => ['required', 'string', 'max:1000'],
            'is_static'      => ['nullable', 'boolean'],
            'static_value'   => ['nullable', 'required_if:is_static,1', 'string', 'max:255'],
            'target'         => ['nullable', 'required_if:is_static,0', 'numeric', 'min:0'],
            'suffix'         => ['nullable', 'string', 'max:10'],
            'decimal_places' => ['nullable', 'integer', 'min:0', 'max:3'],
            'bar_percentage' => ['required', 'integer', 'min:0', 'max:100'],
            'icon'           => ['required', 'string', 'max:100'],
            'is_featured'    => ['nullable', 'boolean'],
            'order'          => ['nullable', 'integer', 'min:0'],
            'is_active'      => ['nullable', 'boolean'],
        ]);

        $data['is_static'] = $request->boolean('is_static');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active'] = $request->boolean('is_active');
        $data['decimal_places'] = $data['decimal_places'] ?? 0;
        $data['order'] = $data['order'] ?? 0;

        return $data;
    }
}