<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\LandingSectionHeader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LandingSectionHeaderController extends Controller
{
    /**
     * Tampilkan daftar section (Dashboard / Solusi / Contact) dengan tombol Edit masing-masing.
     */
    public function index(): View
    {
        $headers = collect(LandingSectionHeader::SECTIONS)
            ->map(fn ($label, $key) => [
                'key'    => $key,
                'label'  => $label,
                'header' => LandingSectionHeader::forKey($key),
            ]);

        return view('superadmin.landing-section-headers.index', compact('headers'));
    }

    public function edit(string $key): View
    {
        abort_unless(array_key_exists($key, LandingSectionHeader::SECTIONS), 404);

        $header = LandingSectionHeader::forKey($key);
        $label  = LandingSectionHeader::SECTIONS[$key];

        return view('superadmin.landing-section-headers.edit', compact('header', 'key', 'label'));
    }

    public function update(Request $request, string $key): RedirectResponse
    {
        abort_unless(array_key_exists($key, LandingSectionHeader::SECTIONS), 404);

        $header = LandingSectionHeader::forKey($key);

        $data = $request->validate([
            'badge'                  => ['required', 'string', 'max:100'],
            'title_normal'           => ['required', 'string', 'max:255'],
            'title_gradient'         => ['required', 'string', 'max:255'],
            'subtitle'               => ['required', 'string', 'max:1000'],
            'button_primary_text'    => ['nullable', 'string', 'max:100'],
            'button_primary_url'     => ['nullable', 'string', 'max:255'],
            'button_secondary_text'  => ['nullable', 'string', 'max:100'],
            'button_secondary_url'   => ['nullable', 'string', 'max:255'],
        ]);

        $header->update($data);

        return redirect()
            ->route('superadmin.landing-section-headers.index')
            ->with('success', 'Header section "' . LandingSectionHeader::SECTIONS[$key] . '" berhasil diperbarui.');
    }
}
