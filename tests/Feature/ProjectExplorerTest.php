<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Technology;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProjectExplorerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createSiteContext();
    }

    public function test_it_lists_published_projects_only(): void
    {
        Project::factory()->create(['title' => 'Visible Project']);
        Project::factory()->draft()->create(['title' => 'Hidden Project']);

        Livewire::test('project-explorer')
            ->assertSee('Visible Project')
            ->assertDontSee('Hidden Project');
    }

    public function test_it_filters_projects_by_category(): void
    {
        $web = ProjectCategory::factory()->create(['name' => 'Web', 'slug' => 'web']);
        $mobile = ProjectCategory::factory()->create(['name' => 'Mobile', 'slug' => 'mobile']);

        Project::factory()->for($web, 'category')->create(['title' => 'Web Platform']);
        Project::factory()->for($mobile, 'category')->create(['title' => 'Mobile App']);

        Livewire::test('project-explorer')
            ->set('category', 'web')
            ->assertSee('Web Platform')
            ->assertDontSee('Mobile App');
    }

    public function test_it_filters_projects_by_technology(): void
    {
        $laravel = Technology::factory()->create(['name' => 'Laravel', 'slug' => 'laravel']);
        $vue = Technology::factory()->create(['name' => 'Vue', 'slug' => 'vue']);

        $laravelProject = Project::factory()->create(['title' => 'Laravel Project']);
        $laravelProject->technologies()->attach($laravel);

        $vueProject = Project::factory()->create(['title' => 'Vue Project']);
        $vueProject->technologies()->attach($vue);

        Livewire::test('project-explorer')
            ->set('technology', 'laravel')
            ->assertSee('Laravel Project')
            ->assertDontSee('Vue Project');
    }

    public function test_it_searches_titles_excerpts_and_technologies(): void
    {
        $project = Project::factory()->create([
            'title' => 'Atlas Analytics',
            'excerpt' => 'A reporting dashboard.',
        ]);
        Project::factory()->create(['title' => 'Unrelated Work']);

        Livewire::test('project-explorer')
            ->set('search', 'Atlas')
            ->assertSee('Atlas Analytics')
            ->assertDontSee('Unrelated Work');
    }

    public function test_clearing_filters_restores_the_full_archive(): void
    {
        $category = ProjectCategory::factory()->create(['slug' => 'web']);
        Project::factory()->for($category, 'category')->create(['title' => 'Web Project']);
        Project::factory()->create(['title' => 'Other Project']);

        Livewire::test('project-explorer')
            ->set('category', 'web')
            ->assertSee('Web Project')
            ->assertDontSee('Other Project')
            ->call('clearFilters')
            ->assertSet('category', '')
            ->assertSee('Other Project');
    }

    public function test_it_paginates_large_archives(): void
    {
        $perPage = config('digital-dimension.pagination.projects');
        Project::factory()->count($perPage + 1)->create();

        $component = Livewire::test('project-explorer');

        $this->assertCount($perPage, $component->instance()->projects->items());
        $this->assertSame($perPage + 1, $component->instance()->projects->total());
    }
}
