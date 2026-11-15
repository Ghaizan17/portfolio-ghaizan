<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Technology;
use Illuminate\Contracts\View\View;

class ProjectController extends Controller
{
    public function index(): View
    {
        return view('pages.projects.index', [
            'profile' => Profile::current(),
            'categories' => ProjectCategory::query()->ordered()->get(),
            'technologies' => Technology::query()
                ->ordered()
                ->whereHas('projects', fn ($query) => $query->published())
                ->get(),
            'total' => Project::query()->published()->count(),
        ]);
    }

    public function show(string $slug): View
    {
        $project = Project::query()
            ->published()
            ->with([
                'category',
                'technologies',
                'media',
                'features',
                'processes',
                'stories',
                'feedback' => fn ($query) => $query->where('is_published', true),
                'testimonials' => fn ($query) => $query->where('is_published', true),
            ])
            ->where('slug', $slug)
            ->firstOrFail();

        $nextProject = Project::query()
            ->published()
            ->with(['category'])
            ->whereKeyNot($project->getKey())
            ->ordered()
            ->first();

        return view('pages.projects.show', [
            'profile' => Profile::current(),
            'project' => $project,
            'nextProject' => $nextProject,
        ]);
    }
}
