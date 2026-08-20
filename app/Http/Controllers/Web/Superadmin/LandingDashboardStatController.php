<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\LandingDashboardStat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LandingDashboardStatController extends Controller
{
    public const COLORS = ['blue', 'cyan', 'purple', 'green', 'orange', 'red'];

    public function index(): View
    {
        $stats = LandingDashboardStat::ordered()->paginate(15);

        return view('superadmin.landing-dashboard-stats.index', compact('stats'));
    }

    public function create(): View
    {
        return view('superadmin.landing-dashboard-stats.create', ['colors' => self::COLORS]);
    }

    public function store(Request $request): RedirectResponse
    {
        LandingDashboardStat::create($this->validated($request));

        return redirect()
            ->route('superadmin.landing-dashboard-stats.index')
            ->with('success', 'Stat card berhasil ditambahkan.');
    }

    public function edit(LandingDashboardStat $landingDashboardStat): View
    {
        return view('superadmin.landing-dashboard-stats.edit', [
            'stat'   => $landingDashboardStat,
            'colors' => self::COLORS,
        ]);
    }

    public function update(Request $request, LandingDashboardStat $landingDashboardStat): RedirectResponse
    {
        $landingDashboardStat->update($this->validated($request));

        return redirect()
            ->route('superadmin.landing-dashboard-stats.index')
            ->with('success', 'Stat card berhasil diperbarui.');
    }

    public function destroy(LandingDashboardStat $landingDashboardStat): RedirectResponse
    {
        $landingDashboardStat->delete();

        return redirect()
            ->route('superadmin.landing-dashboard-stats.index')
            ->with('success', 'Stat card berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'label'           => ['required', 'string', 'max:100'],
            'value'           => ['required', 'string', 'max:50'],
            'trend_text'      => ['required', 'string', 'max:100'],
            'trend_direction' => ['required', 'string', 'in:up,down'],
            'icon'            => ['required', 'string', 'max:100'],
            'color'           => ['required', 'string', 'in:' . implode(',', self::COLORS)],
            'order'           => ['nullable', 'integer', 'min:0'],
            'is_active'       => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['order'] = $data['order'] ?? 0;

        return $data;
    }
}
