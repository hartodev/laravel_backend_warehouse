<?php

namespace App\Http\Controllers\Web\Landing;

use App\Http\Controllers\Controller;
use App\Models\LandingBenefit;
use App\Models\LandingCtaFeature;
use App\Models\LandingDashboardActivity;
use App\Models\LandingDashboardProduct;
use App\Models\LandingDashboardStat;
use App\Models\LandingFaq;
use App\Models\LandingFeature;
use App\Models\LandingHero;
use App\Models\LandingHeroHighlight;
use App\Models\LandingSectionHeader;
use App\Models\LandingSolution;
use App\Models\LandingStat;
use App\Models\LandingTestimonial;
use App\Models\LandingWorkflowStep;

class LandingController extends Controller
{
    public function index()
    {
        // Section yang sudah ada sebelumnya
        $stats         = LandingStat::active()->ordered()->get();
        $testimonials  = LandingTestimonial::active()->ordered()->get();
        $faqs          = LandingFaq::active()->ordered()->get();
        $features      = LandingFeature::active()->ordered()->get();
        $benefits      = LandingBenefit::active()->ordered()->get();
        $workflowSteps = LandingWorkflowStep::active()->ordered()->get();

        // Hero (Home)
        $hero           = LandingHero::singleton();
        $heroHighlights = LandingHeroHighlight::active()->ordered()->get();

        // Header section yang reusable (Dashboard / Solusi / Contact)
        $dashboardHeader = LandingSectionHeader::forKey('dashboard');
        $solutionHeader  = LandingSectionHeader::forKey('solution');
        $contactHeader   = LandingSectionHeader::forKey('contact');

        // Dashboard preview
        $dashboardStats      = LandingDashboardStat::active()->ordered()->get();
        $dashboardActivities = LandingDashboardActivity::active()->ordered()->get();
        $dashboardProducts   = LandingDashboardProduct::active()->ordered()->get();

        // Solusi (bento grid)
        $solutions = LandingSolution::active()->ordered()->with('inventoryItems')->get();

        // Contact / CTA
        $ctaFeatures = LandingCtaFeature::active()->ordered()->get();

        return view('frontend.landing.index', compact(
            'stats',
            'testimonials',
            'faqs',
            'features',
            'benefits',
            'workflowSteps',
            'hero',
            'heroHighlights',
            'dashboardHeader',
            'solutionHeader',
            'contactHeader',
            'dashboardStats',
            'dashboardActivities',
            'dashboardProducts',
            'solutions',
            'ctaFeatures'
        ));
    }
}