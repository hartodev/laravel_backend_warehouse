<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\LandingSolution;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LandingSolutionController extends Controller
{
    public const COLORS       = ['blue', 'cyan', 'purple', 'green', 'orange', 'red'];
    public const SIZES        = ['sm', 'md', 'lg'];
    public const VISUAL_TYPES = ['none', 'inventory', 'chart'];
    public const INVENTORY_COLORS = ['blue', 'green', 'yellow'];

    public function index(): View
    {
        $solutions = LandingSolution::ordered()->withCount('inventoryItems')->paginate(15);

        return view('superadmin.landing-solutions.index', compact('solutions'));
    }

    public function create(): View
    {
        return view('superadmin.landing-solutions.create', $this->formOptions());
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $solution = LandingSolution::create($data);

        $this->syncInventoryItems($solution, $request);

        return redirect()
            ->route('superadmin.landing-solutions.index')
            ->with('success', 'Kartu solusi berhasil ditambahkan.');
    }

    public function edit(LandingSolution $landingSolution): View
    {
        $landingSolution->load('inventoryItems');

        return view('superadmin.landing-solutions.edit', [
            'solution' => $landingSolution,
            ...$this->formOptions(),
        ]);
    }

    public function update(Request $request, LandingSolution $landingSolution): RedirectResponse
    {
        $data = $this->validated($request);

        $landingSolution->update($data);

        $this->syncInventoryItems($landingSolution, $request);

        return redirect()
            ->route('superadmin.landing-solutions.index')
            ->with('success', 'Kartu solusi berhasil diperbarui.');
    }

    public function destroy(LandingSolution $landingSolution): RedirectResponse
    {
        $landingSolution->delete(); // inventory items ikut terhapus (cascadeOnDelete di migration)

        return redirect()
            ->route('superadmin.landing-solutions.index')
            ->with('success', 'Kartu solusi berhasil dihapus.');
    }

    private function formOptions(): array
    {
        return [
            'colors'           => self::COLORS,
            'sizes'            => self::SIZES,
            'visualTypes'      => self::VISUAL_TYPES,
            'inventoryColors'  => self::INVENTORY_COLORS,
        ];
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],
            'icon'        => ['required', 'string', 'max:100'],
            'color'       => ['required', 'string', 'in:' . implode(',', self::COLORS)],
            'size'        => ['required', 'string', 'in:' . implode(',', self::SIZES)],
            'visual_type' => ['required', 'string', 'in:' . implode(',', self::VISUAL_TYPES)],
            'chart_data'  => ['nullable', 'string', 'max:255'],
            'order'       => ['nullable', 'integer', 'min:0'],
            'is_active'   => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['order'] = $data['order'] ?? 0;

        // chart_data cuma relevan kalau visual_type = chart
        if ($data['visual_type'] !== 'chart') {
            $data['chart_data'] = null;
        }

        return $data;
    }

    /**
     * Sinkronkan baris-baris mini inventory (name/stock/color) kalau visual_type = inventory.
     * Kalau bukan "inventory", semua baris lama dihapus supaya tidak nyangkut di database.
     */
    private function syncInventoryItems(LandingSolution $solution, Request $request): void
    {
        if ($solution->visual_type !== 'inventory') {
            $solution->inventoryItems()->delete();

            return;
        }

        $rows = $request->input('inventory', []);

        $solution->inventoryItems()->delete();

        foreach ($rows as $i => $row) {
            if (blank($row['name'] ?? null)) {
                continue;
            }

            $solution->inventoryItems()->create([
                'name'  => $row['name'],
                'stock' => $row['stock'] ?? '',
                'color' => in_array($row['color'] ?? null, self::INVENTORY_COLORS, true) ? $row['color'] : 'blue',
                'order' => $i,
            ]);
        }
    }
}
