<?php

namespace Tests\Feature;

use App\Models\GuestbookEntry;
use App\Models\Post;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createSiteContext();
    }

    public function test_every_public_page_renders_successfully(): void
    {
        $routes = [
            'home',
            'about',
            'skills',
            'projects.index',
            'certificates',
            'experience',
            'resume',
            'contact',
            'guestbook',
            'testimonials',
            'journal.index',
        ];

        foreach ($routes as $route) {
            $this->get(route($route))->assertOk();
        }
    }

    public function test_published_project_detail_page_renders(): void
    {
        $project = Project::factory()->create(['title' => 'Atlas API Platform']);

        $this->get(route('projects.show', $project))
            ->assertOk()
            ->assertSee('Atlas API Platform');
    }

    public function test_draft_projects_are_not_publicly_visible(): void
    {
        $project = Project::factory()->draft()->create();

        $this->get(route('projects.show', $project))->assertNotFound();
    }

    public function test_published_journal_post_renders_and_draft_posts_are_hidden(): void
    {
        $published = Post::factory()->create(['title' => 'Shipping With Confidence']);
        $draft = Post::factory()->draft()->create();

        $this->get(route('journal.show', $published))
            ->assertOk()
            ->assertSee('Shipping With Confidence');

        $this->get(route('journal.show', $draft))->assertNotFound();
    }

    public function test_guestbook_only_shows_approved_entries(): void
    {
        GuestbookEntry::factory()->approved()->create(['name' => 'Approved Visitor']);
        GuestbookEntry::factory()->create(['name' => 'Pending Visitor']);

        $this->get(route('guestbook'))
            ->assertOk()
            ->assertSee('Approved Visitor')
            ->assertDontSee('Pending Visitor');
    }

    public function test_sitemap_lists_published_content_and_excludes_drafts(): void
    {
        $project = Project::factory()->create();
        $draftProject = Project::factory()->draft()->create();
        $post = Post::factory()->create();
        $draftPost = Post::factory()->draft()->create();

        $response = $this->get(route('sitemap'));

        $response->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee(route('projects.show', $project), false)
            ->assertSee(route('journal.show', $post), false)
            ->assertDontSee(route('projects.show', $draftProject), false)
            ->assertDontSee(route('journal.show', $draftPost), false);
    }

    public function test_robots_txt_disallows_admin_and_links_the_sitemap(): void
    {
        $this->get(route('robots'))
            ->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertSee('Disallow: /admin')
            ->assertSee(route('sitemap'));
    }
}
