<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Education;
use App\Models\Experience;
use App\Models\GuestbookEntry;
use App\Models\Profile;
use App\Models\Resume;
use App\Models\SkillCategory;
use App\Models\Testimonial;
use Illuminate\Contracts\View\View;

class PageController extends Controller
{
    public function about(): View
    {
        return view('pages.about', [
            'profile' => Profile::current(),
            'educations' => Education::query()->ordered()->get(),
            'experiences' => Experience::query()->published()->limit(5)->get(),
            'certificateCount' => Certificate::query()->published()->count(),
        ]);
    }

    public function skills(): View
    {
        return view('pages.skills', [
            'profile' => Profile::current(),
            'skillCategories' => SkillCategory::query()
                ->ordered()
                ->with(['skills' => fn ($query) => $query->where('is_active', true)->with('project')])
                ->whereHas('skills', fn ($query) => $query->where('is_active', true))
                ->get(),
        ]);
    }

    public function certificates(): View
    {
        $certificates = Certificate::query()
            ->published()
            ->with('media')
            ->get();

        return view('pages.certificates', [
            'profile' => Profile::current(),
            'certificates' => $certificates,
            'groupedCertificates' => $certificates->groupBy(fn (Certificate $certificate): string => $certificate->category ?: 'Certification'),
        ]);
    }

    public function experience(): View
    {
        $experiences = Experience::query()->published()->with('media')->get();

        return view('pages.experience', [
            'profile' => Profile::current(),
            'experiences' => $experiences,
            'groupedExperiences' => $experiences->groupBy('category'),
            'educations' => Education::query()->ordered()->get(),
        ]);
    }

    public function resume(): View
    {
        return view('pages.resume', [
            'profile' => Profile::current(),
            'resume' => Resume::active(),
            'educations' => Education::query()->ordered()->get(),
            'experiences' => Experience::query()->published()->limit(6)->get(),
            'skillCategories' => SkillCategory::query()
                ->ordered()
                ->with(['skills' => fn ($query) => $query->where('is_active', true)])
                ->whereHas('skills', fn ($query) => $query->where('is_active', true))
                ->get(),
        ]);
    }

    public function contact(): View
    {
        return view('pages.contact', [
            'profile' => Profile::current(),
        ]);
    }

    public function guestbook(): View
    {
        return view('pages.guestbook', [
            'profile' => Profile::current(),
            'entries' => GuestbookEntry::query()
                ->approved()
                ->paginate(config('digital-dimension.pagination.guestbook')),
            'totalApproved' => GuestbookEntry::query()->approved()->count(),
        ]);
    }

    public function testimonials(): View
    {
        $testimonials = Testimonial::query()
            ->published()
            ->with('project')
            ->get();

        return view('pages.testimonials', [
            'profile' => Profile::current(),
            'testimonials' => $testimonials,
            'groupedTestimonials' => $testimonials->groupBy('relationship'),
        ]);
    }
}
