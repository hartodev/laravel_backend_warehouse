<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\LandingCtaFeature;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LandingCtaFeatureController extends Controller
{
    public function index(): View
    {
        $features = LandingCtaFeature::ordered()->paginate(15);

        return view('superadmin.landing-cta-features.index', compact('features'));
    }

    public function create(): View
    {
        return view('superadmin.landing-cta-features.create');
    }

    public function store(Request $request): RedirectResponse
    {
        LandingCtaFeature::create($this->validated($request));

        return redirect()
            ->route('superadmin.landing-cta-features.index')
            ->with('success', 'Poin CTA berhasil ditambahkan.');
    }

    public function edit(LandingCtaFeature $landingCtaFeature): View
    {
        return view('superadmin.landing-cta-features.edit', ['feature' => $landingCtaFeature]);
    }

    public function update(Request $request, LandingCtaFeature $landingCtaFeature): RedirectResponse
    {
        $landingCtaFeature->update($this->validated($request));

        return redirect()
            ->route('superadmin.landing-cta-features.index')
            ->with('success', 'Poin CTA berhasil diperbarui.');
    }

    public function destroy(LandingCtaFeature $landingCtaFeature): RedirectResponse
    {
        $landingCtaFeature->delete();

        return redirect()
            ->route('superadmin.landing-cta-features.index')
            ->with('success', 'Poin CTA berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'text'      => ['required', 'string', 'max:255'],
            'icon'      => ['nullable', 'string', 'max:100'],
            'order'     => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['icon'] = $data['icon'] ?: 'check-circle-2';
        $data['is_active'] = $request->boolean('is_active');
        $data['order'] = $data['order'] ?? 0;

        return $data;
    }
}
