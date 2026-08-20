<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\LandingDashboardActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LandingDashboardActivityController extends Controller
{
    public const COLORS = ['blue', 'cyan', 'purple', 'green', 'orange', 'red'];

    public function index(): View
    {
        $activities = LandingDashboardActivity::ordered()->paginate(15);

        return view('superadmin.landing-dashboard-activities.index', compact('activities'));
    }

    public function create(): View
    {
        return view('superadmin.landing-dashboard-activities.create', ['colors' => self::COLORS]);
    }

    public function store(Request $request): RedirectResponse
    {
        LandingDashboardActivity::create($this->validated($request));

        return redirect()
            ->route('superadmin.landing-dashboard-activities.index')
            ->with('success', 'Aktivitas berhasil ditambahkan.');
    }

    public function edit(LandingDashboardActivity $landingDashboardActivity): View
    {
        return view('superadmin.landing-dashboard-activities.edit', [
            'activity' => $landingDashboardActivity,
            'colors'   => self::COLORS,
        ]);
    }

    public function update(Request $request, LandingDashboardActivity $landingDashboardActivity): RedirectResponse
    {
        $landingDashboardActivity->update($this->validated($request));

        return redirect()
            ->route('superadmin.landing-dashboard-activities.index')
            ->with('success', 'Aktivitas berhasil diperbarui.');
    }

    public function destroy(LandingDashboardActivity $landingDashboardActivity): RedirectResponse
    {
        $landingDashboardActivity->delete();

        return redirect()
            ->route('superadmin.landing-dashboard-activities.index')
            ->with('success', 'Aktivitas berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'time_text'   => ['required', 'string', 'max:100'],
            'icon'        => ['required', 'string', 'max:100'],
            'color'       => ['required', 'string', 'in:' . implode(',', self::COLORS)],
            'value_text'  => ['required', 'string', 'max:20'],
            'value_color' => ['required', 'string', 'in:' . implode(',', self::COLORS)],
            'order'       => ['nullable', 'integer', 'min:0'],
            'is_active'   => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['order'] = $data['order'] ?? 0;

        return $data;
    }
}
