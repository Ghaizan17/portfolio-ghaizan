<?php

namespace Database\Factories;

use App\Models\Experience;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Experience>
 */
class ExperienceFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->jobTitle(),
            'organization' => fake()->company(),
            'position' => fake()->jobTitle(),
            'category' => Experience::CATEGORY_ORGANIZATION,
            'start_date' => now()->subYear(),
            'end_date' => now(),
            'is_current' => false,
            'description' => fake()->paragraph(),
            'is_published' => true,
            'sort_order' => 0,
        ];
    }
}
