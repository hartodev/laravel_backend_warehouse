<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LandingTestimonial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LandingTestimonialController extends Controller
{
    public const COLORS = ['blue', 'purple', 'green', 'cyan', 'orange'];

    public function index(): View
    {
        $testimonials = LandingTestimonial::ordered()->paginate(10);

        return view('admin.landing-testimonials.index', compact('testimonials'));
    }

    public function create(): View
    {
        return view('admin.landing-testimonials.create', ['colors' => self::COLORS]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        LandingTestimonial::create($data);

        return redirect()
            ->route('admin.landing-testimonials.index')
            ->with('success', 'Testimoni berhasil ditambahkan.');
    }

    public function edit(LandingTestimonial $landingTestimonial): View
    {
        return view('admin.landing-testimonials.edit', [
            'testimonial' => $landingTestimonial,
            'colors'      => self::COLORS,
        ]);
    }

    public function update(Request $request, LandingTestimonial $landingTestimonial): RedirectResponse
    {
        $data = $this->validated($request);

        $landingTestimonial->update($data);

        return redirect()
            ->route('admin.landing-testimonials.index')
            ->with('success', 'Testimoni berhasil diperbarui.');
    }

    public function destroy(LandingTestimonial $landingTestimonial): RedirectResponse
    {
        $landingTestimonial->delete();

        return redirect()
            ->route('admin.landing-testimonials.index')
            ->with('success', 'Testimoni berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'role'         => ['required', 'string', 'max:255'],
            'quote'        => ['required', 'string', 'max:1000'],
            'rating'       => ['required', 'integer', 'min:1', 'max:5'],
            'initials'     => ['required', 'string', 'max:5'],
            'avatar_color' => ['required', 'string', 'in:' . implode(',', self::COLORS)],
            'is_featured'  => ['nullable', 'boolean'],
            'order'        => ['nullable', 'integer', 'min:0'],
            'is_active'    => ['nullable', 'boolean'],
        ]);

        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active'] = $request->boolean('is_active');
        $data['order'] = $data['order'] ?? 0;

        return $data;
    }
}
