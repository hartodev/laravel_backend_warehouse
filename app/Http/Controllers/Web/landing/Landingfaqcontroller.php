<?php

namespace App\Http\Controllers\Web\Landing;

use App\Http\Controllers\Controller;
use App\Models\LandingFaq;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LandingFaqController extends Controller
{
    public function index(): View
    {
        $faqs = LandingFaq::ordered()->paginate(10);

        return view('frontend.landing-faqs.index', compact('faqs'));
    }

    public function create(): View
    {
        return view('frontend.landing-faqs.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        LandingFaq::create($data);

        return redirect()
            ->route('admin.landing-faqs.index')
            ->with('success', 'FAQ berhasil ditambahkan.');
    }

    public function edit(LandingFaq $landingFaq): View
    {
        return view('frontend.landing-faqs.edit', ['faq' => $landingFaq]);
    }

    public function update(Request $request, LandingFaq $landingFaq): RedirectResponse
    {
        $data = $this->validated($request);

        $landingFaq->update($data);

        return redirect()
            ->route('admin.landing-faqs.index')
            ->with('success', 'FAQ berhasil diperbarui.');
    }

    public function destroy(LandingFaq $landingFaq): RedirectResponse
    {
        $landingFaq->delete();

        return redirect()
            ->route('admin.landing-faqs.index')
            ->with('success', 'FAQ berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'question'  => ['required', 'string', 'max:255'],
            'answer'    => ['required', 'string', 'max:2000'],
            'order'     => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['order'] = $data['order'] ?? 0;

        return $data;
    }
}