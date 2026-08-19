<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LandingFeature;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LandingFeatureController extends Controller
{
    public const COLORS = ['blue', 'cyan', 'purple', 'green', 'orange'];

    public function index(): View
    {
        $features = LandingFeature::ordered()->paginate(10);

        return view('admin.landing-features.index', compact('features'));
    }

    public function create(): View
    {
        return view('admin.landing-features.create', ['colors' => self::COLORS]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        LandingFeature::create($data);

        return redirect()
            ->route('admin.landing-features.index')
            ->with('success', 'Fitur berhasil ditambahkan.');
    }

    public function edit(LandingFeature $landingFeature): View
    {
        return view('admin.landing-features.edit', [
            'feature' => $landingFeature,
            'colors'  => self::COLORS,
        ]);
    }

    public function update(Request $request, LandingFeature $landingFeature): RedirectResponse
    {
        $data = $this->validated($request);

        $landingFeature->update($data);

        return redirect()
            ->route('admin.landing-features.index')
            ->with('success', 'Fitur berhasil diperbarui.');
    }

    public function destroy(LandingFeature $landingFeature): RedirectResponse
    {
        $landingFeature->delete();

        return redirect()
            ->route('admin.landing-features.index')
            ->with('success', 'Fitur berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],
            'icon'        => ['required', 'string', 'max:100'],
            'color'       => ['required', 'string', 'in:' . implode(',', self::COLORS)],
            'order'       => ['nullable', 'integer', 'min:0'],
            'is_active'   => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['order'] = $data['order'] ?? 0;

        return $data;
    }
}
