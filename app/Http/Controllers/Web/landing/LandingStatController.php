<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LandingStat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LandingStatController extends Controller
{
    public function index(): View
    {
        $stats = LandingStat::ordered()->paginate(10);

        return view('admin.landing-stats.index', compact('stats'));
    }

    public function create(): View
    {
        return view('admin.landing-stats.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        LandingStat::create($data);

        return redirect()
            ->route('admin.landing-stats.index')
            ->with('success', 'Stat berhasil ditambahkan.');
    }

    public function edit(LandingStat $landingStat): View
    {
        return view('admin.landing-stats.edit', ['stat' => $landingStat]);
    }

    public function update(Request $request, LandingStat $landingStat): RedirectResponse
    {
        $data = $this->validated($request);

        $landingStat->update($data);

        return redirect()
            ->route('admin.landing-stats.index')
            ->with('success', 'Stat berhasil diperbarui.');
    }

    public function destroy(LandingStat $landingStat): RedirectResponse
    {
        $landingStat->delete();

        return redirect()
            ->route('admin.landing-stats.index')
            ->with('success', 'Stat berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'label'          => ['required', 'string', 'max:255'],
            'is_static'      => ['nullable', 'boolean'],
            'static_value'   => ['nullable', 'required_if:is_static,1', 'string', 'max:255'],
            'target'         => ['nullable', 'required_if:is_static,0', 'numeric', 'min:0'],
            'suffix'         => ['nullable', 'string', 'max:10'],
            'decimal_places' => ['nullable', 'integer', 'min:0', 'max:3'],
            'bar_percentage' => ['required', 'integer', 'min:0', 'max:100'],
            'order'          => ['nullable', 'integer', 'min:0'],
            'is_active'      => ['nullable', 'boolean'],
        ]);

        $data['is_static'] = $request->boolean('is_static');
        $data['is_active'] = $request->boolean('is_active');
        $data['decimal_places'] = $data['decimal_places'] ?? 0;
        $data['order'] = $data['order'] ?? 0;

        return $data;
    }
}
