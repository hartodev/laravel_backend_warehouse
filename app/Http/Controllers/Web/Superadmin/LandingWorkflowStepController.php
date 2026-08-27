<?php

namespace App\Http\Controllers\Web\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\LandingWorkflowStep;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LandingWorkflowStepController extends Controller
{
    public const COLORS = ['blue', 'cyan', 'purple', 'green', 'orange', 'red'];

    public function index(): View
    {
        $steps = LandingWorkflowStep::ordered()->paginate(15);

        return view('superadmin.landing-workflow-steps.index', compact('steps'));
    }

    public function create(): View
    {
        return view('superadmin.landing-workflow-steps.create', ['colors' => self::COLORS]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        LandingWorkflowStep::create($data);

        return redirect()
            ->route('superadmin.landing-workflow-steps.index')
            ->with('success', 'Langkah workflow berhasil ditambahkan.');
    }

    public function edit(LandingWorkflowStep $landingWorkflowStep): View
    {
        return view('superadmin.landing-workflow-steps.edit', [
            'step'   => $landingWorkflowStep,
            'colors' => self::COLORS,
        ]);
    }

    public function update(Request $request, LandingWorkflowStep $landingWorkflowStep): RedirectResponse
    {
        $data = $this->validated($request);

        $landingWorkflowStep->update($data);

        return redirect()
            ->route('superadmin.landing-workflow-steps.index')
            ->with('success', 'Langkah workflow berhasil diperbarui.');
    }

    public function destroy(LandingWorkflowStep $landingWorkflowStep): RedirectResponse
    {
        $landingWorkflowStep->delete();

        return redirect()
            ->route('superadmin.landing-workflow-steps.index')
            ->with('success', 'Langkah workflow berhasil dihapus.');
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