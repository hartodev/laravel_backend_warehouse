<?php

namespace App\Http\Controllers\Web\Landing;

use App\Http\Controllers\Controller;
use App\Models\LandingFaq;
use App\Models\LandingFeature;
use App\Models\LandingStat;
use App\Models\LandingTestimonial;

class LandingController extends Controller
{
    public function index()
    {
        $stats        = LandingStat::active()->ordered()->get();
        $testimonials = LandingTestimonial::active()->ordered()->get();
        $faqs         = LandingFaq::active()->ordered()->get();
        $features     = LandingFeature::active()->ordered()->get();

        return view('landing.index', compact('stats', 'testimonials', 'faqs', 'features'));
    }
}
