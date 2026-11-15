<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Experience;
use App\Models\Post;
use App\Models\Profile;
use App\Models\Project;
use App\Models\SkillCategory;
use App\Models\Technology;
use App\Models\Testimonial;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $featuredProjects = Project::query()
            ->published()
            ->with(['category', 'technologies', 'media'])
            ->where('featured', true)
            ->ordered()
            ->limit(3)
            ->get();

        if ($featuredProjects->isEmpty()) {
            $featuredProjects = Project::query()
                ->published()
                ->with(['category', 'technologies', 'media'])
                ->ordered()
                ->limit(3)
                ->get();
        }

        return view('pages.home', [
            'profile' => Profile::current(),
            'featuredProjects' => $featuredProjects,
            'skillCategories' => SkillCategory::query()
                ->ordered()
                ->with(['skills' => fn ($query) => $query->where('is_active', true)])
                ->whereHas('skills', fn ($query) => $query->where('is_active', true))
                ->get(),
            'experiences' => Experience::query()->published()->limit(4)->get(),
            'certificates' => Certificate::query()->published()->with('media')->limit(4)->get(),
            'testimonials' => Testimonial::query()->published()->with('project')->limit(3)->get(),
            'posts' => Post::query()->published()->ordered()->with('category')->limit(3)->get(),
            'technologies' => Technology::query()->ordered()->limit(16)->get(),
            'stats' => [
                'projects' => Project::query()->published()->count(),
                'certificates' => Certificate::query()->published()->count(),
                'articles' => Post::query()->published()->count(),
            ],
        ]);
    }
}
