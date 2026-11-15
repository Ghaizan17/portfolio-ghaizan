<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Project;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $staticPages = collect([
            ['route' => 'home', 'priority' => '1.0', 'changefreq' => 'weekly'],
            ['route' => 'about', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['route' => 'skills', 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['route' => 'projects.index', 'priority' => '0.9', 'changefreq' => 'weekly'],
            ['route' => 'certificates', 'priority' => '0.6', 'changefreq' => 'monthly'],
            ['route' => 'experience', 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['route' => 'resume', 'priority' => '0.5', 'changefreq' => 'monthly'],
            ['route' => 'testimonials', 'priority' => '0.5', 'changefreq' => 'monthly'],
            ['route' => 'journal.index', 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['route' => 'contact', 'priority' => '0.8', 'changefreq' => 'yearly'],
            ['route' => 'guestbook', 'priority' => '0.5', 'changefreq' => 'weekly'],
        ])->map(fn (array $page): array => [
            'loc' => route($page['route']),
            'lastmod' => now()->toAtomString(),
            'changefreq' => $page['changefreq'],
            'priority' => $page['priority'],
        ]);

        $projects = Project::query()
            ->published()
            ->ordered()
            ->get()
            ->map(fn (Project $project): array => [
                'loc' => route('projects.show', $project),
                'lastmod' => $project->updated_at?->toAtomString() ?? now()->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => $project->featured ? '0.8' : '0.6',
            ]);

        $posts = Post::query()
            ->published()
            ->ordered()
            ->get()
            ->map(fn (Post $post): array => [
                'loc' => route('journal.show', $post),
                'lastmod' => $post->updated_at?->toAtomString() ?? now()->toAtomString(),
                'changefreq' => 'yearly',
                'priority' => '0.6',
            ]);

        $urls = $staticPages->concat($projects)->concat($posts);

        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
