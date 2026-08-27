<?php

namespace App\Http\Controllers\Web\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\LandingContactLead;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LandingContactLeadController extends Controller
{
    public function index(Request $request): View
    {
        $leads = LandingContactLead::query()
            ->status($request->get('status'))
            ->search($request->get('q'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $counts = [
            'all'       => LandingContactLead::count(),
            'new'       => LandingContactLead::status(LandingContactLead::STATUS_NEW)->count(),
            'contacted' => LandingContactLead::status(LandingContactLead::STATUS_CONTACTED)->count(),
            'closed'    => LandingContactLead::status(LandingContactLead::STATUS_CLOSED)->count(),
        ];

        return view('superadmin.landing-leads.index', compact('leads', 'counts'));
    }

    public function show(LandingContactLead $landingLead): View
    {
        return view('superadmin.landing-leads.show', ['lead' => $landingLead]);
    }

    public function update(Request $request, LandingContactLead $landingLead): RedirectResponse
    {
        $data = $request->validate([
            'status'     => ['required', 'in:' . implode(',', LandingContactLead::STATUSES)],
            'admin_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $data['handled_at'] = now();
        $data['handled_by'] = $request->user()->id;

        $landingLead->update($data);

        return back()->with('success', 'Status lead berhasil diperbarui.');
    }

    public function destroy(LandingContactLead $landingLead): RedirectResponse
    {
        $landingLead->delete();

        return redirect()
            ->route('superadmin.landing-leads.index')
            ->with('success', 'Lead berhasil dihapus.');
    }
}