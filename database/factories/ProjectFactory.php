<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Technology;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(3);

        return [
            'project_category_id' => ProjectCategory::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'excerpt' => fake()->paragraph(),
            'description' => fake()->paragraphs(3, true),
            'problem_statement' => fake()->paragraph(),
            'objectives' => fake()->sentence(),
            'role' => 'Full-Stack Developer',
            'year' => (int) fake()->year(),
            'featured' => false,
            'is_published' => true,
            'published_at' => now()->subDay(),
            'sort_order' => 0,
        ];
    }

    public function featured(): static
    {
        return $this->state(fn (): array => ['featured' => true]);
    }

    public function draft(): static
    {
        return $this->state(fn (): array => [
            'is_published' => false,
            'published_at' => null,
        ]);
    }

    public function withTechnologies(int $count = 3): static
    {
        return $this->afterCreating(function (Project $project) use ($count): void {
            $project->technologies()->attach(
                Technology::factory()->count($count)->create()->pluck('id')
            );
        });
    }
}
