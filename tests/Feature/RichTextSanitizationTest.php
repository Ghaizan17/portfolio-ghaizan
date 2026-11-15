<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RichTextSanitizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createSiteContext();
    }

    public function test_project_html_is_sanitized_on_save(): void
    {
        $project = Project::factory()->create([
            'description' => '<p>Safe content</p><script>alert("xss")</script>',
            'problem_statement' => '<p onclick="steal()">Problem</p>',
        ]);

        $this->assertStringContainsString('Safe content', $project->description);
        $this->assertStringNotContainsString('<script', $project->description);
        $this->assertStringNotContainsString('onclick', $project->problem_statement);
    }

    public function test_post_html_is_sanitized_on_save(): void
    {
        $post = Post::factory()->create([
            'content' => '<p>Legitimate paragraph</p><iframe src="https://evil.example"></iframe>',
        ]);

        $this->assertStringContainsString('Legitimate paragraph', $post->content);
        $this->assertStringNotContainsString('<iframe', $post->content);
    }

    public function test_sanitized_content_is_rendered_on_the_public_project_page(): void
    {
        $project = Project::factory()->create([
            'description' => '<p>Rendered safely</p><script>alert("xss")</script>',
        ]);

        $this->get(route('projects.show', $project))
            ->assertOk()
            ->assertSee('Rendered safely')
            ->assertDontSee('<script>alert("xss")</script>', false);
    }
}
