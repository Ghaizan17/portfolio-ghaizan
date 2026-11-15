<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DemoContentSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminResourcesSmokeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createSiteContext();
        $this->seed(DemoContentSeeder::class);
    }

    public function test_every_admin_resource_page_renders_for_an_administrator(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin);

        $resources = Filament::getPanel('admin')->getResources();

        $this->assertNotEmpty($resources);

        foreach ($resources as $resource) {
            $pages = $resource::getPages();

            $this->get($resource::getUrl('index'))
                ->assertOk();

            if (isset($pages['create'])) {
                $this->get($resource::getUrl('create'))->assertOk();
            }

            $record = $resource::getModel()::query()->first();

            if (! $record) {
                continue;
            }

            if (isset($pages['view'])) {
                $this->get($resource::getUrl('view', ['record' => $record]))->assertOk();
            }

            if (isset($pages['edit'])) {
                $this->get($resource::getUrl('edit', ['record' => $record]))->assertOk();
            }
        }
    }
}
