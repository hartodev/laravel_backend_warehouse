<?php

namespace App\Http\Controllers\Web\Landing;

use App\Http\Controllers\Controller;
use App\Models\LandingContactLead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LandingContactController extends Controller
{
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['required', 'email', 'max:255'],
            'phone'   => ['nullable', 'string', 'max:30'],
            'company' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
            // honeypot anti-spam sederhana: field tersembunyi, harus kosong
            'website' => ['prohibited'],
        ]);

        unset($data['website']);

        $lead = LandingContactLead::create($data + [
            'status' => LandingContactLead::STATUS_NEW,
            'source' => 'cta_contact_sales',
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Terima kasih! Tim sales kami akan segera menghubungi Anda.',
                'id'      => $lead->id,
            ]);
        }

        return back()->with('success', 'Terima kasih! Tim sales kami akan segera menghubungi Anda.');
    }
}
